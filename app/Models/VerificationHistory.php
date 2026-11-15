<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VerificationHistory extends Model{public $timestamps=false;protected $guarded=[];protected function casts():array{return ['performed_at'=>'datetime','context'=>'array'];}}
