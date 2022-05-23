<?php

namespace Mdhesari\LaravelQueryFilters\Traits;

use Mdhesari\LaravelQueryFilters\Scopes\ExpandScope;

trait HasExpandScope
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ExpandScope);
    }
}