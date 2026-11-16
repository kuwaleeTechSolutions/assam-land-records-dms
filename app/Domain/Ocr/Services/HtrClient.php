<?php
namespace App\Domain\Ocr\Services;
use Illuminate\Support\Facades\Http;
use RuntimeException;
class HtrClient
{
    public function configured(): bool { return filled(config('ocr.htr_url')); }
    public function recognize(string $image): array
    {
        if(!$this->configured()) throw new RuntimeException('Assamese HTR service is not configured.');
        $request=Http::timeout(config('ocr.htr_timeout'));
        if(config('ocr.htr_token')) $request=$request->withToken(config('ocr.htr_token'));
        $response=$request->attach('image',fopen($image,'rb'),basename($image))->post(rtrim(config('ocr.htr_url'),'/').'/v1/recognize',['language'=>'asm','document_domain'=>'assam_land_records']);
        $response->throw();
        return ['text'=>(string)$response->json('text',''),'confidence'=>(float)$response->json('confidence',0),'metadata'=>$response->json('metadata',[])];
    }
}
