<?php

namespace Mdhesari\LaravelQueryFilters\Actions;

use Illuminate\Support\Carbon;
use Mdhesari\LaravelQueryFilters\LaravelQueryFiltersServiceProvider;

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
