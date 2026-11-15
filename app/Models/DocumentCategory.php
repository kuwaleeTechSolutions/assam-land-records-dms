<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DocumentCategory extends Model{protected $guarded=[];protected function casts():array{return ['requires_verification'=>'boolean','is_active'=>'boolean'];}}
