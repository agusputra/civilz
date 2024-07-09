<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

// use App\Support\Helper;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Data';

    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->autocomplete(false),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->readOnlyOn('edit'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->autocomplete(false),
                // Forms\Components\Select::make('type')->options([
                //     User::TYPE_GUEST => User::TYPE_GUEST,
                //     User::TYPE_STUDENT => User::TYPE_STUDENT,
                //     User::TYPE_ADMIN => User::TYPE_ADMIN,
                // ])->required(),
                // Forms\Components\Select::make('roles')
                //     ->relationship('roles', 'name')
                //     ->multiple()
                //     ->preload()
                //     ->searchable(),
                // Forms\Components\CheckboxList::make('roles')
                //     ->relationship('roles', 'name')
                //     ->searchable(),
                // Forms\Components\FileUpload::make('avatar')
                //     ->disk('s3')
                //     ->avatar()
                //     ->placeholder(function ($record) {
                //         $src = $record->avatar ?? User::DEFAULT_USER_AVATAR;

                //         return new HtmlString("<img src='".$src."'>");
                //     }),
                // ->saveUploadedFileUsing(function ($state) {
                //     $file = collect($state)->first();

                //     return Helper::uploadToS3($file);
                // }),
                Forms\Components\Section::make('Info')
                    ->schema([
                        Forms\Components\TextInput::make('remember_token')->readOnly(),
                        Forms\Components\DateTimePicker::make('email_verified_at')->readOnly(),
                        Forms\Components\DateTimePicker::make('deleted_at')->readOnly(),
                        Forms\Components\DateTimePicker::make('created_at')->readOnly(),
                        Forms\Components\DateTimePicker::make('updated_at')->readOnly(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                // Tables\Columns\ImageColumn::make('avatar')
                //     ->disk('s3')
                //     ->getStateUsing(function (User $record): string {
                //         $avatar = $record->avatar;

                //         if (empty($avatar)) {
                //             $avatar = User::DEFAULT_USER_AVATAR;
                //         } elseif (strpos($avatar, '//') === 0) {
                //             $avatar = 'https:'.$avatar;
                //         }

                //         return $avatar;
                //     })
                //     ->checkFileExistence(false)
                //     ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\GroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
            // 'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'access_view_nav',
            'access_main_nav',
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user() && auth()->user()->can('access_main_nav_user');
    }
}
