<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AuditLog extends Model{public $timestamps=false;protected $guarded=[];protected function casts():array{return ['actor_roles_snapshot'=>'array','old_values'=>'array','new_values'=>'array','context'=>'array','occurred_at'=>'datetime'];}}
