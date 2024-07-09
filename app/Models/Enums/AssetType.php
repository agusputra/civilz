<?php

namespace App\Models\Enums;

enum AssetType: string
{
    use Enum;

    case PROPERTY = 'property';
    case VEHICLE = 'vehicle';
}
