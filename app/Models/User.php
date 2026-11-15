<?php

namespace App\Models;

use App\Domain\Identity\Enums\UserStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, HasUlids, Notifiable;

    protected $fillable = [
        'username', 'name', 'email', 'mobile', 'password', 'status',
        'mfa_required', 'failed_login_count', 'locked_until',
        'last_login_at', 'last_login_ip', 'password_changed_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'status' => UserStatus::class,
            'password' => 'hashed',
            'mfa_required' => 'boolean',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'mfa_secret' => 'encrypted',
            'mfa_recovery_codes' => 'encrypted:array',
            'mfa_confirmed_at' => 'datetime',
        ];
    }

    public function uniqueIds(): array { return ['ulid']; }
    public function getRouteKeyName(): string { return 'ulid'; }

    public function geographicScopes(): HasMany
    {
        return $this->hasMany(UserGeographicScope::class);
    }

    public function permissionOverrides(): HasMany
    {
        return $this->hasMany(UserPermissionOverride::class);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::Active
            && (! $this->locked_until || $this->locked_until->isPast());
    }
}
