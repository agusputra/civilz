<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Test extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.test';

    public static function canAccess(): bool
    {
        return false;

        /** @var \App\Models\User */
        $user = auth()->user();

        return $user->hasRole('super_admin');
    }
}
