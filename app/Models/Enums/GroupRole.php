<?php

namespace App\Models\Enums;

enum GroupRole: string
{
    use Enum;

    case HUSBAND = 'husband';
    case WIFE = 'wife';
    case CHILD = 'child';
    case OTHER = 'other';
}
