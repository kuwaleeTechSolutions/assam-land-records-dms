<?php

namespace App\Models;

use App\Domain\Authorization\Enums\PermissionEffect;
use Illuminate\Database\Eloquent\Model;

class UserPermissionOverride extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['effect' => PermissionEffect::class, 'assigned_at' => 'datetime', 'expires_at' => 'datetime', 'revoked_at' => 'datetime']; }
    public function isActive(): bool { return ! $this->revoked_at && (! $this->expires_at || $this->expires_at->isFuture()); }
}
