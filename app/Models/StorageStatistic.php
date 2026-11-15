<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StorageStatistic extends Model{protected $guarded=[];protected function casts():array{return ['snapshot_date'=>'date'];}}
