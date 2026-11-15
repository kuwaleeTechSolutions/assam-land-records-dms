<?php
namespace App\Models;
use App\Domain\Verification\Enums\{VerificationStage,VerificationStatus};use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class DocumentVerification extends Model{use HasUlids;protected $guarded=[];protected function casts():array{return ['stage'=>VerificationStage::class,'status'=>VerificationStatus::class,'assigned_at'=>'datetime','started_at'=>'datetime','decision_at'=>'datetime'];}public function uniqueIds():array{return ['ulid'];}public function document(){return $this->belongsTo(Document::class);}public function version(){return $this->belongsTo(DocumentVersion::class,'document_version_id');}public function history(){return $this->hasMany(VerificationHistory::class);}}
