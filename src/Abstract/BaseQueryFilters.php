<?php

namespace Mdhesari\LaravelQueryFilters\Abstract;

use Mdhesari\LaravelQueryFilters\Actions\ApplyQueryFilters;

abstract class BaseQueryFilters
{
    public function __construct(
        private ApplyQueryFilters $applyQueryFilters
    )
    {
        //
    }

    public function __invoke($query, array $data)
    {
        ($this->applyQueryFilters)($query, $data);

        return $query;
    }

    protected function user(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return Auth::user();
    }
}
