<?php

namespace App\Domain\Identity\Enums;

enum UserStatus: string
{
    case Active = 'ACTIVE';
    case Suspended = 'SUSPENDED';
    case Locked = 'LOCKED';
    case Disabled = 'DISABLED';

    public function canAuthenticate(): bool
    {
        return $this === self::Active;
    }
}
