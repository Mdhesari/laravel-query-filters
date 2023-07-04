<?php

namespace Mdhesari\LaravelQueryFilters\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class QueryDTO extends ValidatedDTO
{
    protected function defaults(): array
    {
        return [
            //
        ];
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected function rules(): array
    {
        return [
            'oldest'    => 'nullable|nullable|bool',
            'date_from' => 'nullable|string|numeric',
            'date_to'   => 'nullable|string|numeric',
            'order_by'  => 'nullable|string',
            'expand'    => 'nullable|string',
            's'         => 'nullable|string',
            'per_page'  => 'nullable|string|numeric',
            'user_id'   => 'nullable|string|numeric',
        ];
    }
}