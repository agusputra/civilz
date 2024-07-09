<?php

namespace App\Models\Enums;

enum MaritalStatus: string
{
    use Enum;

    case SINGLE = 'single';
    case MARRIED = 'married';
    case DIVORCED = 'divorced';
    case WIDOWED = 'widowed';
}
