<?php

namespace App\Models\Enums;

enum VehicleType: string
{
    use Enum;

    case CAR = 'car';
    case MOTORCYCLE = 'motorcycle';
    case PEDICAB = 'pedicab';
}
