<?php
namespace App\Domain\Ocr\Services;
use Symfony\Component\Process\Process;
use RuntimeException;
class TesseractPrintedOcr
{
    public function recognize(string $image): array
    {
        $p=new Process([config('ocr.tesseract_binary'),'--dpi',(string)config('ocr.dpi'),$image,'stdout','-l',config('ocr.languages'),'--oem','1','--psm',(string)config('ocr.page_segmentation_mode'),'tsv']);
        $p->setTimeout(600); $p->run();
        if(!$p->isSuccessful()) throw new RuntimeException('Tesseract failed: '.$p->getErrorOutput());
        $lines=preg_split('/\R/u',trim($p->getOutput())); $words=[]; $scores=[];
        foreach(array_slice($lines,1) as $line){$c=str_getcsv($line,"\t"); if(count($c)<12)continue; $text=trim($c[11]); $conf=(float)$c[10]; if($text!==''&&$conf>=0){$words[]=$text;$scores[]=$conf;}}
        return ['text'=>implode(' ',$words),'confidence'=>$scores?array_sum($scores)/count($scores):0.0,'metadata'=>['words'=>count($words)]];
    }
}
