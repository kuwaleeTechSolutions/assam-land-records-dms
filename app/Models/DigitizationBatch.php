<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
class DigitizationBatch extends Model {
 use HasUlids; protected $guarded=[];
 protected function casts():array{return ['opened_at'=>'datetime','closed_at'=>'datetime','processing_started_at'=>'datetime','processing_finished_at'=>'datetime','source_zip_size'=>'integer','total_files'=>'integer','processed_files'=>'integer','failed_files'=>'integer','verification_files'=>'integer'];}
 public function uniqueIds():array{return ['ulid'];} public function getRouteKeyName():string{return 'ulid';}
 public function district(){return $this->belongsTo(District::class);} public function lac(){return $this->belongsTo(Lac::class);}
 public function revenueCircle(){return $this->belongsTo(RevenueCircle::class);} public function mouza(){return $this->belongsTo(Mouza::class);}
 public function village(){return $this->belongsTo(Village::class);} public function documentType(){return $this->belongsTo(DocumentType::class);}
 public function category(){return $this->belongsTo(DocumentCategory::class,'document_category_id');}
 public function items(){return $this->hasMany(DigitizationBatchItem::class);} public function documents(){return $this->hasMany(Document::class);}
}