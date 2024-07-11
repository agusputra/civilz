<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamilyResource\Pages;
use App\Filament\Resources\FamilyResource\RelationManagers;
use App\Models\Group;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FamilyResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Group::class;

    // protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Data';

    protected static ?int $navigationSort = 40;

    public static function getNavigationLabel(): string
    {
        return __('admin.family.nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin.family.page.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('address')
                    ->label(__('admin.family.page.form.address')),
                Forms\Components\TextInput::make('expenses')
                    ->label(__('admin.family.page.form.expenses'))
                    ->numeric()
                    ->integer()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('distance_to_mosque')
                    ->label(__('admin.family.page.form.distance_to_mosque'))
                    ->numeric()
                    ->integer()
                    ->suffix('meter(s)'),
                Forms\Components\TextInput::make('prayer_frequency')
                    ->label(__('admin.family.page.form.prayer_frequency'))
                    ->numeric()
                    ->integer()
                    ->suffix('per / hari'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('parents.name')
                    ->label(__('admin.family.page.table.parents')),
                Tables\Columns\TextColumn::make('children_count')->counts('children')
                    ->sortable()
                    ->label(__('admin.family.page.table.children_count')),
                Tables\Columns\TextColumn::make('others_count')->counts('others')
                    ->sortable()
                    ->label(__('admin.family.page.table.others_count')),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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
            RelationManagers\UsersRelationManager::class,
            RelationManagers\AssetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamilies::route('/'),
            'create' => Pages\CreateFamily::route('/create'),
            'edit' => Pages\EditFamily::route('/{record}/edit'),
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
        return auth()->user() && auth()->user()->can('access_main_nav_group');
    }
}
