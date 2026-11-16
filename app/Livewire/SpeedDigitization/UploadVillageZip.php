<?php
namespace App\Livewire\SpeedDigitization;
use App\Jobs\ProcessDigitizationZip; use App\Models\{DigitizationBatch,District,DocumentCategory,DocumentType,Lac,Mouza,RevenueCircle,Village};
use Illuminate\Support\Facades\Storage; use Illuminate\Support\Str; use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout; use Livewire\Component; use Livewire\WithFileUploads;
#[Layout('layouts.app')]
class UploadVillageZip extends Component {
 use WithFileUploads; public $zip;
 public ?int $district_id=null,$lac_id=null,$revenue_circle_id=null,$mouza_id=null,$village_id=null,$document_category_id=null,$document_type_id=null,$default_record_year=null;
 public string $description='';
 public function mount():void{abort_unless(app(\App\Domain\Authorization\Services\PermissionResolver::class)->allows(auth()->user(),'bulk.upload'),403);}
 public function updatedDistrictId():void{$this->reset(['lac_id','revenue_circle_id','mouza_id','village_id']);}
 public function updatedRevenueCircleId():void{$this->reset(['mouza_id','village_id']);}
 public function updatedMouzaId():void{$this->reset('village_id');}
 public function updatedDocumentCategoryId():void{$this->reset('document_type_id');}
 public function save():void{
  $data=$this->validate([
   'district_id'=>['required','exists:districts,id'],'lac_id'=>['required','exists:lacs,id'],'revenue_circle_id'=>['required','exists:revenue_circles,id'],
   'mouza_id'=>['required','exists:mouzas,id'],'village_id'=>['required','exists:villages,id'],'document_category_id'=>['required','exists:document_categories,id'],
   'document_type_id'=>['required','exists:document_types,id'],'default_record_year'=>['nullable','integer','min:1700','max:'.now()->year],
   'description'=>['nullable','string','max:5000'],'zip'=>['required','file','mimes:zip','max:'.config('dms.max_zip_upload_kb',1048576)],
  ]);
  $mappedCircle=\DB::table('village_circle_assignments')->where(['village_id'=>$data['village_id'],'revenue_circle_id'=>$data['revenue_circle_id'],'is_current'=>true])->exists();
  $mappedLac=\DB::table('village_lac_assignments')->where(['village_id'=>$data['village_id'],'lac_id'=>$data['lac_id'],'is_current'=>true])->exists();
  abort_unless($mappedCircle && $mappedLac,422,'The selected Village, Circle and LAC mapping is not valid.');
  $disk=config('dms.quarantine_disk'); $name=(string)Str::ulid().'.zip'; $key=trim(config('dms.bulk_zip_prefix','dms-bulk-zips'),'/').'/'.$name;
  $stream=fopen($this->zip->getRealPath(),'rb'); Storage::disk($disk)->writeStream($key,$stream); if(is_resource($stream))fclose($stream);
  $path=Storage::disk($disk)->path($key);
  $batch=DigitizationBatch::create(array_merge(collect($data)->except('zip')->all(),[
   'ulid'=>(string)Str::ulid(),'batch_reference'=>'SPD-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),'status'=>'QUEUED','opened_by'=>auth()->id(),'opened_at'=>now(),
   'source_disk'=>$disk,'source_zip_key'=>$key,'source_zip_name'=>basename($this->zip->getClientOriginalName()),'source_zip_sha256'=>hash_file('sha256',$path),'source_zip_size'=>filesize($path),
  ]));
  ProcessDigitizationZip::dispatch($batch->id); session()->flash('success','Village ZIP accepted and queued.'); $this->redirectRoute('speed.batches.show',$batch,navigate:true);
 }
 public function render(){return view('livewire.speed-digitization.upload-village-zip',[
  'districts'=>District::where('is_active',true)->orderBy('name_en')->get(),'lacs'=>Lac::where('is_active',true)->when($this->district_id,fn($q)=>$q->where('district_id',$this->district_id))->orderBy('constituency_no')->get(),
  'circles'=>RevenueCircle::where('is_active',true)->when($this->district_id,fn($q)=>$q->where('district_id',$this->district_id))->orderBy('name_en')->get(),
  'mouzas'=>Mouza::where('is_active',true)->when($this->revenue_circle_id,fn($q)=>$q->where('revenue_circle_id',$this->revenue_circle_id))->orderBy('name_en')->get(),
  'villages'=>Village::where('is_active',true)->when($this->mouza_id,fn($q)=>$q->where('mouza_id',$this->mouza_id))->orderBy('name_en')->get(),
  'categories'=>DocumentCategory::where('is_active',true)->orderBy('sort_order')->get(),'types'=>DocumentType::where('is_active',true)->when($this->document_category_id,fn($q)=>$q->where('document_category_id',$this->document_category_id))->orderBy('sort_order')->get(),
 ]);}
}