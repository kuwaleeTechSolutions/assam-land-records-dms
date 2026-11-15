<?php

namespace App\Domain\Authorization\Enums;

enum PermissionEffect: string
{
    case Allow = 'ALLOW';
    case Deny = 'DENY';
}
