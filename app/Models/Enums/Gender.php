<?php

namespace App\Models\Enums;

enum Gender: string
{
    use Enum;

    case MALE = 'male';
    case FEMALE = 'female';
}
