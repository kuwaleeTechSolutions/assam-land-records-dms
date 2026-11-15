<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_ulid', 'user_id', 'event_type', 'severity', 'result',
        'username_snapshot', 'ip_address', 'user_agent', 'request_id',
        'context', 'occurred_at',
    ];

    protected function casts(): array
    {
        return ['context' => 'array', 'occurred_at' => 'datetime'];
    }
}
