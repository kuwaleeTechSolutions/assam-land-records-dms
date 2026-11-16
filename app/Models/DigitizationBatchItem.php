<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model;
class DigitizationBatchItem extends Model {
    use HasUlids; 
    protected $guarded=[]; 
    protected function casts():array{
        return ['size_bytes'=>'integer','overall_confidence'=>'decimal:2','started_at'=>'datetime','finished_at'=>'datetime'];
    } 
    public function uniqueIds():array{return ['ulid'];} public function batch(){return $this->belongsTo(DigitizationBatch::class,'digitization_batch_id');} 
    // public function document(){return $this->belongsTo(Document::class);} 
    public function extractions(){return $this->hasMany(DocumentExtraction::class);} 
    public function document(){return $this->belongsTo(\App\Models\Document::class);}
}