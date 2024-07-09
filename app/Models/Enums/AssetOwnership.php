<?php

namespace App\Models\Enums;

enum AssetOwnership: string
{
    use Enum;

    case OWNED = 'owned';
    case RENTED = 'rented';
}
