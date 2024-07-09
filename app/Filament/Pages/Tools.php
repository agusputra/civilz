<?php

namespace App\Filament\Pages;

use App\Imports\GroupsImport;
use App\Imports\UsersImport;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;

class Tools extends Page // implements HasForms
{
    // use InteractsWithForms;

    protected static string $routePath = '/dashboard-tools';

    protected static ?string $title = 'Tools';

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Settings';

    protected static string $view = 'filament.pages.tools';

    protected static ?int $navigationSort = 60;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Import Users')
                ->label('Import Users')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->required()
                        ->disk('s3'),
                    // ->directory('tmp')
                    // ->storeFiles(false),
                ])
                ->action(function (array $data) {
                    $import = new UsersImport();

                    // The notification is not working.
                    // Notification::make()
                    //     ->title('Importing...')
                    //     ->body('Check log for progress')
                    //     ->success()
                    //     ->send();

                    Excel::import($import, $data['file'], 's3', \Maatwebsite\Excel\Excel::XLSX);
                }),
            Actions\Action::make('Import Groups')
                ->label('Import Groups')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->required()
                        ->disk('s3'),
                ])
                ->action(function (array $data) {
                    $import = new GroupsImport();

                    Excel::import($import, $data['file'], 's3', \Maatwebsite\Excel\Excel::XLSX);
                }),
        ];
    }

    // protected function getFormSchema(): array
    // {
    //     return [
    //         Forms\Components\TextInput::make('somefield'),
    //     ];
    // }

    // public function submit()
    // {
    //     /** @var \Filament\Forms\Form */
    //     $form = $this->getForm('form');
    // }

    public static function canAccess(): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return $user->hasRole('super_admin');
    }
}
