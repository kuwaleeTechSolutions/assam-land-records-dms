<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids; use Illuminate\Database\Eloquent\Model;
class DocumentExtraction extends Model {
    use HasUlids; protected $guarded=[]; protected function casts():array{return ['overall_confidence'=>'decimal:2','metadata'=>'array'];} public function uniqueIds():array{return ['ulid'];} public function fields(){return $this->hasMany(DocumentExtractedField::class);} 
    public function item(){return $this->belongsTo(\App\Models\DigitizationBatchItem::class,'digitization_batch_item_id');}
    public function document(){return $this->belongsTo(\App\Models\Document::class);}

    // Add inside App\Models\DigitizationBatchItem if extraction relation is missing:
    public function batch(){return $this->belongsTo(\App\Models\DigitizationBatch::class,'digitization_batch_id');}
    public function extraction(){return $this->hasOne(\App\Models\DocumentExtraction::class);}

}