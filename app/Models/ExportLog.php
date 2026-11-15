<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class ExportLog extends Model{use HasUlids;protected $guarded=[];protected function casts():array{return ['filters'=>'array','contains_sensitive_data'=>'boolean','requested_at'=>'datetime','completed_at'=>'datetime','expires_at'=>'datetime','downloaded_at'=>'datetime'];}public function uniqueIds(): array{return ['ulid'];}}
