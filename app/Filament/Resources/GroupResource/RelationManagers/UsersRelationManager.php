<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Models\Enums\EducationLevel;
use App\Models\Enums\Gender;
use App\Models\Enums\GroupRole;
use App\Models\Enums\MaritalStatus;
use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resident.page.label', ['name' => $ownerRecord->name]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->autocomplete(false),
                Forms\Components\Hidden::make('email')
                    ->required()
                    ->afterStateHydrated(function (Component $component, ?string $state, string $operation): void {
                        if ($operation === 'create' && empty($state)) {
                            $random = Str::random(6);
                            $timestamp = now()->timestamp;
                            $component->state("user-$timestamp-$random@example.com");
                        }
                    }),
                Forms\Components\Hidden::make('password')
                    ->afterStateHydrated(function (Component $component, ?string $state, string $operation): void {
                        if ($operation === 'create' && empty($state)) {
                            $random = Str::random(6);
                            $timestamp = now()->timestamp;
                            $component->state("pwd$timestamp$random");
                        }
                    })
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Forms\Components\Select::make('meta.role')
                    ->options(GroupRole::toOptions())
                    ->required()
                    ->afterStateHydrated(function (Component $component, ?string $state, User $user, $livewire) {
                        $group = $livewire->getOwnerRecord();
                        $userGroup = UserGroup::where('user_id', $user->id)->where('group_id', $group->id)->first();

                        if ($userGroup) {
                            $component->state($userGroup->meta
                                ? $userGroup->meta['role'] ?? null
                                : null);
                        }
                    })
                    ->afterStateUpdated(function (Component $component, ?string $state, Get $get, Set $set) {
                        $role = $get('meta.role');
                        $role = GroupRole::tryFrom($role);
                        $gender = $role === GroupRole::HUSBAND
                            ? Gender::MALE
                            : ($role === GroupRole::WIFE ? Gender::FEMALE : null);

                        if ($gender) {
                            $set('gender', $gender->value);
                        }
                    })
                    ->live(),
                Forms\Components\Select::make('gender')
                    ->options(Gender::toOptions()),
                Forms\Components\Select::make('marital_status')
                    ->options(MaritalStatus::toOptions())
                    ->disabled(function (Component $component, ?string $state, Get $get) {
                        $role = $get('meta.role');
                        $role = GroupRole::tryFrom($role);

                        return $role ? $role !== GroupRole::HUSBAND && $role !== GroupRole::WIFE : true;
                    }),
                Forms\Components\TextInput::make('age')
                    ->live(onBlur: true)
                    ->numeric()
                    ->integer(true)
                    ->minValue(0)
                    ->maxValue(150)
                    ->afterStateUpdated(function (Component $component, ?int $state, Get $get, Set $set, $old) {
                        $age = $state;
                        $oldAge = $old;

                        if ($age !== $oldAge) {
                            $dob = now()->subYears($age)->month(1)->day(1);
                            $set('dob', $dob->format('Y-m-d'));
                        }
                    }),
                Forms\Components\DatePicker::make('dob')
                    ->live(onBlur: true, debounce: 800)
                    // Using native(false) is buggy. When the component->live(), the datepicker value increases by 1 day per second.
                    // ->native(false)
                    ->default('2000-01-01')
                    ->afterStateHydrated(function (Component $component, ?string $state, Get $get, Set $set) {
                        $dob = Carbon::parse($state);
                        $age = floor($dob->diffInYears(now()));

                        $set('age', $age);
                        $component->state($state);
                    })
                    ->afterStateUpdated(function (Component $component, ?string $state, Get $get, Set $set, ?string $old) {
                        $dob = Carbon::parse($state);
                        $age = floor($dob->diffInYears(now()));
                        $oldDob = Carbon::parse($old);
                        $oldAge = floor($oldDob->diffInYears(now()));

                        if ($age !== $oldAge) {
                            $set('age', $age);
                        }
                    }),
                Forms\Components\TextInput::make('phone_number'),
                Forms\Components\Select::make('education')
                    ->options(EducationLevel::toOptions()),
                Forms\Components\TextInput::make('occupation'),
                Forms\Components\TextInput::make('income')
                    ->numeric()
                    ->integer()
                    ->prefix('Rp'),
                // Forms\Components\CheckboxList::make('roles')
                //     ->relationship('roles', 'name')
                //     ->searchable(),
                // Forms\Components\FileUpload::make('avatar')
                //     ->disk('s3')
                //     ->avatar()
                //     // ->saveUploadedFileUsing(function ($state) {
                //     //     $file = collect($state)->first();

                //     //     return Helper::uploadToS3($file);
                //     // }),
                //     ->placeholder(function ($record) {
                //         $src = $record->avatar ?? User::DEFAULT_USER_AVATAR;

                //         return new HtmlString("<img src='".$src."'>");
                //     }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn (User $user, $livewire): BelongsToMany => $livewire->getOwnerRecord()->users())
            ->inverseRelationship('groups')
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
                    ->label(__('resident.page.table.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label(__('resident.page.table.role'))
                    ->state(function (User $user): string {
                        return $user->pivot->meta ? $user->pivot->meta['role'] ?? '' : '';
                    }),
                Tables\Columns\TextColumn::make('gender')
                    ->label(__('resident.page.table.gender')),
                // Tables\Columns\TextColumn::make('tmp'),
                // Tables\Columns\TextColumn::make('tmp_json'),
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
}
