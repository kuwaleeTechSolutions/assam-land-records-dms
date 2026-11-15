<?php

namespace App\Models;

use App\Domain\Authorization\Enums\GeographicScopeLevel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class UserGeographicScope extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['scope_level' => GeographicScopeLevel::class, 'assigned_at' => 'datetime', 'expires_at' => 'datetime', 'revoked_at' => 'datetime']; }
    public function uniqueIds(): array { return ['ulid']; }
    public function isActive(): bool { return ! $this->revoked_at && (! $this->expires_at || $this->expires_at->isFuture()); }
}
