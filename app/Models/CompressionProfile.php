<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CompressionProfile extends Model{protected $guarded=[];protected function casts():array{return ['downsampling_allowed'=>'boolean','preserve_fonts'=>'boolean','preserve_metadata'=>'boolean','configuration'=>'array','is_default'=>'boolean'];}}
