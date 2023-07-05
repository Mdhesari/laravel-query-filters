<?php

namespace Mdhesari\LaravelQueryFilters\Contracts;

interface HasFilters
{
    /**
     * Get available columns to filter search
     *
     * @return array|string
     */
    public function getSearchParams(): array|string;
}
