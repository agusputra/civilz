<?php

namespace App\Models\Enums;

enum PropertyLevel: string
{
    use Enum;

    case LEVEL1 = 'Level 1';
    case LEVEL2 = 'Level 2';
    case LEVEL3 = 'Level 3';
    case LEVEL4 = 'Level 4';
}
