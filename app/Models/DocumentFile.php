<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class DocumentFile extends Model{use HasFactory, HasUlids;protected $guarded=[];protected function casts():array{return ['uploaded_at'=>'datetime','processed_at'=>'datetime','size_bytes'=>'integer','page_count'=>'integer'];}public function uniqueIds():array{return ['ulid'];}public function version(){return $this->belongsTo(DocumentVersion::class,'document_version_id');}}
