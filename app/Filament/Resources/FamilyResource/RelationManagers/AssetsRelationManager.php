<?php

namespace App\Filament\Resources\FamilyResource\RelationManagers;

use App\Models\Enums\AssetOwnership;
use App\Models\Enums\AssetType;
use App\Models\Enums\PropertyLevel;
use App\Models\Enums\VehicleType;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.asset.page.label', ['name' => $ownerRecord->name]);
    }

    public function form(Form $form): Form
    {
        $whenAssetTypeIsVehicle = function ($expected = true) {
            return function (Component $component, ?string $state, Get $get, Set $set) use ($expected) {
                $type = $get('type');
                $result = $type ? AssetType::from($type) === AssetType::VEHICLE : false;

                return $result === $expected;
            };
        };

        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label(__('admin.asset.page.form.type'))
                    ->live()
                    ->options(AssetType::toOptions())
                    ->afterStateUpdated(function (Component $component, ?string $state, Get $get, Set $set) {
                        $type = $get('type');

                        if ($type && AssetType::from($type) === AssetType::PROPERTY) {
                            $set('quantity', 1);
                        }
                    }),
                Forms\Components\Select::make('ownership')
                    ->label(__('admin.asset.page.form.ownership'))
                    ->options(AssetOwnership::toOptions())
                    ->disabled($whenAssetTypeIsVehicle()),
                Forms\Components\Select::make('property_level')
                    ->label(__('admin.asset.page.form.property_level'))
                    ->options(PropertyLevel::toOptions())
                    ->disabled($whenAssetTypeIsVehicle()),
                Forms\Components\Select::make('vehicle_type')
                    ->label(__('admin.asset.page.form.vehicle_type'))
                    ->options(VehicleType::toOptions())
                    ->disabled($whenAssetTypeIsVehicle(false)),
                Forms\Components\TextInput::make('quantity')
                    ->label(__('admin.asset.page.form.quantity'))
                    ->numeric()
                    ->integer()
                    ->readOnly($whenAssetTypeIsVehicle(false)),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('admin.asset.page.table.type'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('ownership')
                    ->label(__('admin.asset.page.table.ownership'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('property_level')
                    ->label(__('admin.asset.page.table.property_level'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label(__('admin.asset.page.table.vehicle_type'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('admin.asset.page.table.quantity'))
                    ->sortable(),
            ])
            ->filters([
                //
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
                ]),
            ]);
    }
}
