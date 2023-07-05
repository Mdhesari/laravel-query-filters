<?php

namespace Mdhesari\LaravelQueryFilters\Abstract;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Mdhesari\LaravelQueryFilters\Actions\ApplyQueryFilters;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Exceptions\MissingCastTypeException;

abstract class BaseQueryFilters
{
    public function __construct(
        private ApplyQueryFilters $applyQueryFilters
    ) {
        //
    }

    /**
     * @throws CastTargetException
     * @throws MissingCastTypeException
     * @throws ValidationException
     */
    public function __invoke($query, array $data)
    {
        ($this->applyQueryFilters)($query, $data);

        return $query;
    }

    protected function user(): ?Authenticatable
    {
        return Auth::user();
    }
}
