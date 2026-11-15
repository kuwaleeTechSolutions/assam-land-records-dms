<?php
namespace App\Jobs;
use App\Domain\Storage\Services\IntegrityScanner;use App\Models\DocumentFile;use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Queue\Queueable;
class ScanRepositoryIntegrity implements ShouldQueue{use Queueable;public int $timeout=1200;public function __construct(public int $limit=1000){$this->onQueue('integrity');}public function handle(IntegrityScanner $scanner):void{DocumentFile::whereIn('file_role',['MASTER','PRESERVATION','OPTIMIZED','PREVIEW'])->orderByRaw('COALESCE((SELECT MAX(checked_at) FROM file_integrity_checks WHERE file_integrity_checks.document_file_id = document_files.id), "1970-01-01") ASC')->limit($this->limit)->get()->each(fn($f)=>$scanner->scan($f));}}
