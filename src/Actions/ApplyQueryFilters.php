<?php

namespace Mdhesari\LaravelQueryFilters\Actions;

use Illuminate\Support\Carbon;
use Mdhesari\LaravelQueryFilters\LaravelQueryFiltersServiceProvider;
use Mdhesari\LaravelQueryFilters\Contracts\Expandable;

class ApplyQueryFilters
{
    public function __invoke($query, array $params)
    {
        if ( isset($params['oldest']) ) {
            $query->oldest();
        } else {
            $query->latest();
        }

        if ( isset($params['s']) ) {
            $query->searchLike($this->getSearchParams($query), $params['s']);
        }

        if ( isset($params['date_from']) || isset($params['date_to']) ) {
            $query->whereBetween('created_at', [$this->getFromDate($params), $this->getToDate($params)]);
        }

        if ( isset($params['per_page']) ) {
            $query->getModel()->setPerPage($params['per_page']);
        }

        if ( isset($params['user_id']) && $this->hasUserId($query->getModel()->getFillable()) ) {
            $query->whereUserId($params['user_id']);
        }

        if ( isset($params['expand']) && method_exists($query->getModel(), 'getExpandRelations') ) {
            $relations = $query->getModel()->getExpandRelations();

            $query->with(
                array_filter($params['expand'], fn($expand) => in_array($expand, $relations))
            );
        }

        return $query;
    }

    private function getSearchParams($query): array
    {
        $model = $query->getModel();

        return \Arr::wrap(null ?? method_exists($model, 'getSearchParams') ? $model->getSearchParams() : $model->getRouteKeyName());
    }

    private function getFromDate(array $params): Carbon
    {
        return Carbon::createFromTimestamp($params['date_from']) ?? Carbon::createFromTimestamp($params['date_to'])->subWeek();
    }

    private function getToDate(array $params): Carbon
    {
        return Carbon::createFromTimestamp($params['date_to']) ?? now();
    }

    private function hasUserId($keys): bool
    {
        return in_array('user_id', $keys);
    }
}
