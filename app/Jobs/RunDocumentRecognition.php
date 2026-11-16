<?php
namespace App\Jobs;
use App\Domain\Ocr\Services\{HtrClient,LandRecordFieldExtractor,TesseractPrintedOcr};
use App\Models\{DigitizationBatchItem,DocumentExtraction,DocumentExtractedField,DocumentFile};
use Illuminate\Bus\Queueable; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue; use Illuminate\Queue\SerializesModels; use Illuminate\Support\Facades\Storage; use Illuminate\Support\Str;
use Symfony\Component\Process\Process; use RuntimeException;
class RunDocumentRecognition implements ShouldQueue
{
 use Dispatchable,InteractsWithQueue,Queueable,SerializesModels; public int $timeout=1800; public int $tries=2;
 public function __construct(public int $itemId){}
 public function handle(TesseractPrintedOcr $ocr,HtrClient $htr,LandRecordFieldExtractor $extractor):void
 {
  $item=DigitizationBatchItem::with('document.currentVersion.files')->findOrFail($this->itemId);
  $file=$item->document?->currentVersion?->files?->firstWhere('file_role','MASTER'); if(!$file)throw new RuntimeException('MASTER PDF not found.');
  $item->update(['status'=>'RECOGNIZING','ocr_status'=>'PROCESSING','htr_status'=>$htr->configured()?'PROCESSING':'NOT_CONFIGURED','started_at'=>now()]);
  $pdf=Storage::disk($file->storage_disk)->path($file->storage_key); $dir=sys_get_temp_dir().'/ocr_'.Str::ulid(); mkdir($dir,0700,true);
  try{
   $prefix=$dir.'/page'; $render=new Process([config('ocr.pdftoppm_binary'),'-r',(string)config('ocr.dpi'),'-png',$pdf,$prefix]); $render->setTimeout(900); $render->mustRun();
   $pages=glob($prefix.'-*.png'); if(!$pages)throw new RuntimeException('No PDF pages rendered.');
   $texts=[];$scores=[];$engines=[];
   foreach($pages as $page){$r=$ocr->recognize($page);$best=$r;$engine='tesseract-asm-eng';
    if($htr->configured()){try{$candidate=$htr->recognize($page);if($candidate['confidence']>$best['confidence']){$best=$candidate;$engine='assamese-htr';}}catch(\Throwable $e){report($e);}}
    $texts[]=$best['text'];$scores[]=$best['confidence'];$engines[]=$engine;
   }
   $text=implode("\n",$texts);$confidence=$scores?array_sum($scores)/count($scores):0;
   $extraction=DocumentExtraction::create(['ulid'=>(string)Str::ulid(),'digitization_batch_item_id'=>$item->id,'document_id'=>$item->document_id,'engine'=>implode('+',array_unique($engines)),'raw_text'=>$text,'language'=>'asm+eng','overall_confidence'=>$confidence,'status'=>'STAGED','metadata'=>['pages'=>count($pages)]]);
   foreach($extractor->extract($text,$confidence) as $field){$requires=$field['confidence']<config('ocr.auto_accept_confidence');DocumentExtractedField::create(array_merge($field,['document_extraction_id'=>$extraction->id,'requires_verification'=>$requires,'verification_status'=>$requires?'PENDING':'AUTO_ACCEPTED']));}
   $critical=['patta_no','dag_no','record_year'];$found=$extraction->fields()->pluck('field_name')->all();foreach(array_diff($critical,$found) as $missing)DocumentExtractedField::create(['document_extraction_id'=>$extraction->id,'field_name'=>$missing,'confidence'=>0,'requires_verification'=>true,'verification_status'=>'MISSING']);
   $needs=$extraction->fields()->where('requires_verification',true)->exists();$item->update(['status'=>$needs?'VERIFICATION_REQUIRED':'EXTRACTED','ocr_status'=>'COMPLETED','htr_status'=>$htr->configured()?'COMPLETED':'NOT_CONFIGURED','overall_confidence'=>$confidence,'finished_at'=>now()]);
   if($needs)$item->batch()->increment('verification_files');
  } finally {foreach(glob($dir.'/*')?:[] as $f)@unlink($f);@rmdir($dir);}
  if ($needs) {
        $item->batch()->increment('verification_files');
    } else {
        \App\Jobs\AutoApplyTrustedExtraction::dispatch($extraction->id);
    }
 }

 public function failed(\Throwable $e):void{DigitizationBatchItem::whereKey($this->itemId)->update(['status'=>'FAILED','ocr_status'=>'FAILED','failure_code'=>'RECOGNITION_FAILED','failure_message'=>Str::limit($e->getMessage(),5000),'finished_at'=>now()]);}
}
