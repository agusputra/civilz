<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Casts\Json;

/**
 * CAUTION:
 * When using Filament there are some caveats when using it in the intermediate model.
 *
 * 1. The meta field in the intermediate model (many-to-many) can conflict.
 * Example: If we use meta field on User model, Group model, and UserGroup model, then if using meta.role as table column will not display the role field.
 * Because the meta field in the intermediate model (UserGroup) conflict with the meta field in the User model or Group model.
 * The solution is to get the meta field manually using state method on Filament table column. TextColumn::make('meta')->state(...)
 *
 * 2. The meta field should be added to the fillable property of the intermediate model to make it works properly.
 */
trait HasMeta
{
    public function initializeHasMeta()
    {
        // This meta field should be hidden in the model.
        // The meta attribute is a way to utilize json field as a store to make dynamic attributes in the model without creating the field in db.
        $this->hidden[] = 'meta';

        // Do we need this? Seems not working.
        // For model that is already exists in db and the meta field is null in db, when we do $someModel->meta['someField'] = 'value',
        // then it's not set the meta field in db.
        $this->attributes['meta'] = '{}';

        $this->casts['meta'] = AsArrayObject::class;
    }

    public function createAttributeInMeta($name = null): ?Attribute
    {
        if (empty($name)) {
            return new Attribute();
        }

        return Attribute::make(
            get: function () use ($name) {
                return $this->meta[$name] ?? null;
            },
            set: function ($value) use ($name) {
                if (empty($this->meta)) {
                    $this->meta = [];
                }

                $this->meta[$name] = $value;

                return ['meta' => json_encode($this->meta)];
            }
        );
    }

    public function setFieldInMeta($key, $value)
    {
        if (empty($this->meta)) {
            $this->meta = [];
        }

        return $this->meta[$key] = $value;
    }
}
