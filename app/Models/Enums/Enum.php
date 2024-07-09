<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

trait Enum
{
    public function getNameI18n(): string
    {
        return Str::title($this->name);
    }

    public static function toOptions(): array
    {
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getNameI18n();
        }

        return $options;
    }
}
