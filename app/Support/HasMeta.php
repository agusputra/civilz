<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
    // private ?ArrayObject $metastore = null;

    public function initializeHasMeta()
    {
        // This meta field should be hidden in the model.
        // The meta attribute is a way to utilize json field as a store to make dynamic attributes in the model without creating the field in db.
        $this->hidden[] = 'meta';

        $this->casts['meta'] = AsArrayObject::class;
    }

    public function createAttributeInMeta($name = null, callable $getter = null, callable $setter = null): ?Attribute
    {
        // // TODO: With this we have multiple sources of data.
        // if (is_null($this->metastore)) {
        //     $meta = $this->attributes['meta'] ?? '{}';
        //     $meta = json_decode($meta, true);

        //     $this->metastore = new ArrayObject($meta);
        // }

        if (empty($name)) {
            return new Attribute();
        }

        return Attribute::make(
            get: function (mixed $value, array $attrs) use ($name, $getter) {
                // $value = $this->metastore[$name] ?? null;

                $meta = $attrs['meta'] ?? '{}';
                $meta = json_decode($meta, true);
                $value = $meta[$name] ?? null;

                if (is_callable($getter)) {
                    $value = $getter($value);
                }

                return $value;
            },
            set: function (mixed $value, array $attrs) use ($name, $setter) {
                if (is_callable($setter)) {
                    $value = $setter($value);
                }

                // $this->metastore[$name] = $value;

                $meta = $attrs['meta'] ?? '{}';
                $meta = json_decode($meta, true);
                $meta[$name] = $value;

                // return ['meta' => json_encode($this->metastore)];

                return ['meta' => json_encode($meta)];
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
