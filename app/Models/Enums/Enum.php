<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

trait Enum
{
    public function getNameT(): string
    {
        $name = __('models.enums.'.Str::lower(class_basename(static::class)).'.'.$this->value);

        return $name;
    }

    public static function toOptions(): array
    {
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getNameT();
        }

        return $options;
    }
}
