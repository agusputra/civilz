<?php

use Illuminate\Support\Collection;

class StdArray extends Collection
{
    public function toOptions()
    {
        return $this->map(fn ($value, $key) => [$key, $value])->toArray();
    }
}
