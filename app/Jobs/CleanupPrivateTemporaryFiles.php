<?php
namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Queue\Queueable;use Illuminate\Support\Facades\Storage;
class CleanupPrivateTemporaryFiles implements ShouldQueue{use Queueable;public function __construct(public int $olderThanHours=24){$this->onQueue('maintenance');}public function handle():void{$disk=Storage::disk(config('dms.quarantine_disk','local'));$prefix=trim(config('dms.quarantine_prefix','dms-quarantine'),'/');$cutoff=now()->subHours($this->olderThanHours)->timestamp;foreach($disk->files($prefix) as $file){try{if($disk->lastModified($file)<$cutoff)$disk->delete($file);}catch(\Throwable $e){report($e);}}}}
