<?php

namespace App\Models\Enums;

enum FamilyRole: string
{
    use Enum;

    case HUSBAND = 'husband';
    case WIFE = 'wife';
    case CHILD = 'child';
    case OTHER = 'other';
}
