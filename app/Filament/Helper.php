<?php

namespace App\Filament;

use ArrayObject;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Illuminate\Support\Str;

class Helper
{
    // Use this to prevent error in filament's form schema: "Property type not supported in Livewire for property: [{...}]"
    // If don't want to use this, then hide the meta field (add the meta field to the $hidden array in the model)
    public static function getDefaultTextInputForMeta(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('meta')
            ->readOnly()
            ->hidden()
            // afterStateHydrated's callback's $state parameter will be null for (new ArrayObject([])) or (new ArrayObject())
            ->afterStateHydrated(function (Forms\Components\TextInput $component, ?ArrayObject $state) {
                $component->state(json_encode($state));
            });
    }

    public static function getRepeaterIdField(): Forms\Components\Hidden
    {
        return Forms\Components\Hidden::make('id')
            ->default(function () {
                return Str::random();
            });
    }

    public static function getRepeaterItemLabel(mixed $state, Component $component, string $label): string
    {
        $key = array_search($state, $component->getState());
        $index = array_search($key, array_keys($component->getState()));

        return "$label ".$index + 1;
    }
}
