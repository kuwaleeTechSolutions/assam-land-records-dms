<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUlids;use Illuminate\Database\Eloquent\Model;
class DocumentVersion extends Model{use HasUlids;protected $guarded=[];public function uniqueIds():array{return ['ulid'];}public function document(){return $this->belongsTo(Document::class);}public function files(){return $this->hasMany(DocumentFile::class);}}
