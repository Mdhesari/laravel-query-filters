<?php

namespace Mdhesari\LaravelQueryFilters\DTOs;

use WendellAdriel\ValidatedDTO\ValidatedDTO;

class QueryDTO extends ValidatedDTO
{
    public bool $oldest;

    public string $date_from;

    public string $date_to;

    public string $order_by;

    public string $expand;

    public string $s;

    public int $per_page;

    public int $user_id;

    protected function defaults(): array
    {
        return [
            'oldest' => false,
        ];
    }

    protected function casts(): array
    {
        return [
            'oldest' => 'boolean',
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
            'per_page'  => 'nullable|numeric',
            'user_id'   => 'nullable|numeric',
        ];
    }
}