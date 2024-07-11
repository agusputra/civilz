<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Enums\EducationLevel;
use App\Models\Enums\FamilyRole;
use App\Models\Enums\Gender;
use App\Models\Enums\MaritalStatus;
use App\Support\HasMeta;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasMeta, HasRoles, Notifiable, SoftDeletes;

    public const PANEL_ROLE_SUPER_ADMIN = 'super_admin';

    public const PANEL_ROLE_USER = 'panel_user';

    public const DEFAULT_USER_AVATAR = 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/59/User-avatar.svg/240px-User-avatar.svg.png';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'dob',
        'occupation',
        'phone',
        'education',
        'marital_status',
        'income',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'gender',
        'dob',
        'occupation',
        'phone',
        'education',
        'marital_status',
        'income',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //region Relations

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'user_group')
            ->using(UserGroup::class)
            ->withPivot('id', 'meta', 'tmp', 'tmp_json')
            ->withTimestamps();
    }

    //endregion

    //region Scopes

    public function scopeOfParents(Builder $query): Builder
    {
        return $query->whereHas('groups', function ($query) {
            $query
                ->whereJsonContains('user_group.meta->role', FamilyRole::HUSBAND)
                ->orWhereJsonContains('user_group.meta->role', FamilyRole::WIFE);
        });
    }

    //endregion

    //region Attributes

    public function gender(): Attribute
    {
        return $this->createAttributeInMeta('gender', fn ($value) => Gender::tryFrom($value));
    }

    public function dob(): Attribute
    {
        return $this->createAttributeInMeta('dob');
    }

    public function occupation(): Attribute
    {
        return $this->createAttributeInMeta('occupation');
    }

    public function phone(): Attribute
    {
        return $this->createAttributeInMeta('phone');
    }

    public function education(): Attribute
    {
        return $this->createAttributeInMeta('education', fn ($value) => EducationLevel::tryFrom($value));
    }

    public function maritalStatus(): Attribute
    {
        return $this->createAttributeInMeta('marital_status', fn ($value) => MaritalStatus::tryFrom($value));
    }

    public function income(): Attribute
    {
        return $this->createAttributeInMeta('income');
    }

    //endregion

    //region Functions

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(Utils::getSuperAdminName()) || $this->hasRole(Utils::getPanelUserRoleName());
    }

    //endregion
}
