<?php

namespace App\Models\Enums;

enum EducationLevel: string
{
    use Enum;

    case ELEMENTARY = 'elementary';
    case SECONDARY = 'secondary';
    case HIGH_SCHOOL = 'high_school';
    case COLLEGE = 'college';
}
