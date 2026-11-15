<?php
namespace App\Models;
use App\Domain\BulkImport\Enums\MigrationBatchStatus;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class MigrationBatch extends Model{use HasUlids;protected $guarded=[];protected function casts():array{return ['status'=>MigrationBatchStatus::class,'approved_at'=>'datetime','completed_at'=>'datetime'];}public function uniqueIds():array{return ['ulid'];}public function items(){return $this->hasMany(MigrationBatchItem::class);}}
