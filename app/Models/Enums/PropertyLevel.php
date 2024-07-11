<?php

namespace App\Models\Enums;

enum PropertyLevel: string
{
    use Enum;

    case LEVEL1 = 'level1';
    case LEVEL2 = 'level2';
    case LEVEL3 = 'level3';
    case LEVEL4 = 'level4';
}
