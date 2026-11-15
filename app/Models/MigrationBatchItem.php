<?php
namespace App\Models;
use App\Domain\BulkImport\Enums\MigrationItemStatus;use Illuminate\Database\Eloquent\Model;
class MigrationBatchItem extends Model{protected $guarded=[];protected function casts():array{return ['status'=>MigrationItemStatus::class,'source_payload'=>'array','normalized_payload'=>'array','warnings'=>'array','errors'=>'array','started_at'=>'datetime','completed_at'=>'datetime'];}public function batch(){return $this->belongsTo(MigrationBatch::class,'migration_batch_id');}}
