<?php

namespace App\Models;

use App\Models\Enums\FamilyRole;
use App\Support\HasMeta;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroup extends Pivot
{
    use HasFactory, HasMeta, SoftDeletes;

    protected $fillable = [
        // 'tmp',
        // 'tmp_json',
        'meta',
    ];

    protected $appends = [
        // 'tmp',
        // 'tmp_json',
    ];

    protected $casts = [
        // 'tmp_json' => 'array',
    ];

    //region Relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    //endregion

    //region Attributes

    public function role(): Attribute
    {
        return $this->createAttributeInMeta('role', fn ($value) => FamilyRole::tryFrom($value));
    }

    //endregion
}
