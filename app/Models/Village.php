<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['is_active' => 'boolean', 'valid_from' => 'date', 'valid_to' => 'date']; }
    public function uniqueIds(): array { return ['ulid']; }
    public function getRouteKeyName(): string { return 'ulid'; }
    // Add inside App\Models\Village:
    public function lacs(){return $this->belongsToMany(\App\Models\Lac::class,'village_lac_assignments')->withPivot(['valid_from','valid_to','is_current'])->withTimestamps();}
    public function revenueCircles(){return $this->belongsToMany(\App\Models\RevenueCircle::class,'village_circle_assignments')->withPivot(['valid_from','valid_to','is_current'])->withTimestamps();}
}
