<?php

namespace Mdhesari\LaravelQueryFilters\Contracts;

interface Expandable
{
    public function getExpandRelations(): array;
}