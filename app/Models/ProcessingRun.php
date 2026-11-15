<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class ProcessingRun extends Model{use HasUlids;protected $guarded=[];protected function casts():array{return ['started_at'=>'datetime','finished_at'=>'datetime','input_size'=>'integer','output_size'=>'integer','compression_ratio'=>'decimal:4'];}public function uniqueIds():array{return ['ulid'];}}
