<?php

namespace App\Domain\Authorization\Services;

use App\Domain\Authorization\Enums\PermissionEffect;
use App\Models\User;

class PermissionResolver
{
    public function allows(User $user, string $permission): bool
    {
        if (! $user->isActive()) return false;
        $overrides = $user->permissionOverrides()->where('permission_name', $permission)->get()->filter->isActive();
        if ($overrides->contains(fn ($o) => $o->effect === PermissionEffect::Deny)) return false;
        if ($overrides->contains(fn ($o) => $o->effect === PermissionEffect::Allow)) return true;
        return $user->can($permission);
    }
}
