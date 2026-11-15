<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class BackupRun extends Model{use HasUlids;protected $guarded=[];protected function casts():array{return ['started_at'=>'datetime','completed_at'=>'datetime','verified_at'=>'datetime','restore_tested_at'=>'datetime','metadata'=>'array'];}public function uniqueIds():array{return ['ulid'];}}
