<?php
namespace App\Jobs;
use App\Models\DocumentFile;use Illuminate\Bus\Queueable;use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;
class ProcessDocumentMaster implements ShouldQueue{use FoundationQueueable;public int $tries=3;public array $backoff=[30,120,600];public function __construct(public int $documentFileId){$this->onQueue('pdf-processing');}public function handle():void{$file=DocumentFile::findOrFail($this->documentFileId);if($file->processing_status!=='MASTER_ACCEPTED')return;$file->update(['processing_status'=>'READY_FOR_DERIVATIVES']);}}
