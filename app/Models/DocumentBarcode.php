<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DocumentBarcode extends Model{protected $guarded=[];protected function casts():array{return ['issued_at'=>'datetime','first_printed_at'=>'datetime','last_printed_at'=>'datetime'];}public function document(){return $this->belongsTo(Document::class);}}
