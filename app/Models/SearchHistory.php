<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SearchHistory extends Model{public $timestamps=false;protected $guarded=[];protected function casts():array{return ['filters'=>'array','searched_at'=>'datetime'];}}
