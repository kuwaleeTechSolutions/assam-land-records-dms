<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model;
class Lac extends Model {use HasUlids; protected $guarded=[]; protected function casts():array{return ['is_active'=>'boolean','valid_from'=>'date','valid_to'=>'date'];} public function uniqueIds():array{return ['ulid'];} public function villages(){return $this->belongsToMany(Village::class,'village_lac_assignments')->withPivot(['valid_from','valid_to','is_current'])->withTimestamps();}}