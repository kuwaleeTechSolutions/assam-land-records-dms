<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DocumentExtractedField extends Model {
    protected $guarded=[]; protected function casts():array{return ['confidence'=>'decimal:2','requires_verification'=>'boolean','verified_at'=>'datetime'];}
    public function extraction(){return $this->belongsTo(\App\Models\DocumentExtraction::class,'document_extraction_id');}
    public function trainingSamples(){return $this->hasMany(\App\Models\HtrTrainingSample::class);}
}