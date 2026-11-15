<?php

namespace App\Domain\Authorization\Enums;

enum GeographicScopeLevel: string
{
    case State = 'STATE';
    case District = 'DISTRICT';
    case Subdivision = 'SUBDIVISION';
    case RevenueCircle = 'CIRCLE';
    case Mouza = 'MOUZA';
    case Village = 'VILLAGE';
}
