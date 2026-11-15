<?php
namespace App\Domain\Documents\Services;
use App\Models\{Document,DocumentAccessLog,DocumentFile};use Illuminate\Support\Str;
class DocumentAccessRecorder{public function record(Document $document,string $action,string $result,?DocumentFile $file=null):void{DocumentAccessLog::create(['event_ulid'=>(string)Str::ulid(),'document_id'=>$document->id,'document_version_id'=>$file?->document_version_id,'document_file_id'=>$file?->id,'user_id'=>auth()->id(),'action'=>$action,'result'=>$result,'ip_address'=>request()->ip(),'user_agent'=>Str::limit((string)request()->userAgent(),1000,''),'request_id'=>request()->attributes->get('request_id'),'occurred_at'=>now()]);}}
