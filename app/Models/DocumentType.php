<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DocumentType extends Model{protected $guarded=[];protected function casts():array{return ['requires_patta_no'=>'boolean','requires_dag_no'=>'boolean','requires_record_year'=>'boolean','is_active'=>'boolean'];}}
