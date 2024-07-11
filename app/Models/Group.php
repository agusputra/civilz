<?php

namespace App\Models;

use App\Models\Enums\FamilyRole;
use App\Support\HasMeta;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, HasMeta, SoftDeletes;

    protected $fillable = [
        'temp',
        'address',
        'expenses',
        'distance_to_mosque',
        'prayer_frequency',
    ];

    protected $appends = [
        'temp',
        'address',
        'expenses',
        'distance_to_mosque',
        'prayer_frequency',
    ];

    //region Relations

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_group')
            ->using(UserGroup::class)
            ->withPivot('id', 'meta', 'tmp', 'tmp_json')
            ->withTimestamps();
    }

    public function parents(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('meta->role', FamilyRole::HUSBAND->value)
            ->orWherePivot('meta->role', FamilyRole::WIFE->value);
    }

    public function children(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('meta->role', FamilyRole::CHILD->value);
    }

    public function others(): BelongsToMany
    {
        return $this->users()
            ->wherePivot('meta->role', FamilyRole::OTHER->value);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    //endregion

    //region Scopes

    //

    //endregion

    //region Attributes

    public function temp(): Attribute
    {
        return $this->createAttributeInMeta('temp');
    }

    public function address(): Attribute
    {
        return $this->createAttributeInMeta('address');
    }

    public function expenses(): Attribute
    {
        return $this->createAttributeInMeta('expenses');
    }

    public function distanceToMosque(): Attribute
    {
        return $this->createAttributeInMeta('distance_to_mosque');
    }

    public function prayerFrequency(): Attribute
    {
        return $this->createAttributeInMeta('prayer_frequency');
    }

    //endregion
}
