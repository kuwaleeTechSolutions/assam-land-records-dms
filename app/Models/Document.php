<?php
namespace App\Models;
use App\Domain\Documents\Enums\DocumentStatus;use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Document extends Model{use HasFactory, HasUlids;protected $guarded=[];protected function casts():array{return ['status'=>DocumentStatus::class,'document_date'=>'date','registered_at'=>'datetime'];}public function uniqueIds():array{return ['ulid'];}public function getRouteKeyName():string{return 'ulid';}public function barcode(){return $this->hasOne(DocumentBarcode::class);}public function category(){return $this->belongsTo(DocumentCategory::class);}public function documentType(){return $this->belongsTo(DocumentType::class);}public function district(){return $this->belongsTo(District::class);}public function revenueCircle(){return $this->belongsTo(RevenueCircle::class);}public function mouza(){return $this->belongsTo(Mouza::class);}public function village(){return $this->belongsTo(Village::class);}
public function versions()
{
    return $this->hasMany(DocumentVersion::class);
}

public function currentVersion()
{
    return $this->belongsTo(
        DocumentVersion::class,
        'current_version_id',
    );
}

public function legalHolds()
{
    return $this->hasMany(DocumentLegalHold::class);
}

public function retentionPolicy()
{
    return $this->belongsTo(RetentionPolicy::class);
}
public function lac(){return $this->belongsTo(\App\Models\Lac::class);}
public function digitizationBatch(){return $this->belongsTo(\App\Models\DigitizationBatch::class);}
}
