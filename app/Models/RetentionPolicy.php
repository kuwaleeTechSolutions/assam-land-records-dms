<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RetentionPolicy extends Model{protected $guarded=[];protected function casts():array{return ['retain_permanently'=>'boolean','requires_disposition_approval'=>'boolean','is_active'=>'boolean'];}}
