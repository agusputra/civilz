<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Password;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Send Reset Password Link')
                ->action(function () {
                    Password::sendResetLink([
                        'email' => $this->record->email,
                    ]);
                })
                ->requiresConfirmation(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeFill(array $data): array
    // {
    //     $data['achievements'] = collect($data['achievements'] ?? [])->map(fn ($item) => ['text' => $item])->toArray();

    //     return $data;
    // }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $data['achievements'] = collect($data['achievements'] ?? [])->map(fn ($item) => $item['text'])->toArray();

    //     return $data;
    // }
}
