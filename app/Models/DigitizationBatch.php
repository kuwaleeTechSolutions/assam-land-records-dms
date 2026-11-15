<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class DigitizationBatch extends Model{use HasUlids;protected $guarded=[];protected function casts():array{return ['opened_at'=>'datetime','closed_at'=>'datetime'];}public function uniqueIds():array{return ['ulid'];}}
