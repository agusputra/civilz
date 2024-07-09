<?php

namespace App\Models;

use App\Support\HasMeta;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, HasMeta, SoftDeletes;

    protected $fillable = [
        'type',
        'ownership',
        'property_level',
        'vehicle_type',
        'quantity',
    ];

    protected $appends = [
        'type',
        'ownership',
        'property_level',
        'vehicle_type',
        'quantity',
    ];

    //region Relations

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    //endregion

    //region Attributes

    public function type(): Attribute
    {
        return $this->createAttributeInMeta('type');
    }

    public function ownership(): Attribute
    {
        return $this->createAttributeInMeta('ownership');
    }

    public function propertyLevel(): Attribute
    {
        return $this->createAttributeInMeta('property_level');
    }

    public function vehicleType(): Attribute
    {
        return $this->createAttributeInMeta('vehicle_type');
    }

    public function quantity(): Attribute
    {
        return $this->createAttributeInMeta('quantity');
    }

    //endregion
}
