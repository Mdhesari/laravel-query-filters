<?php

namespace Mdhesari\LaravelQueryFilters\Actions;

use Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mdhesari\LaravelQueryFilters\DTOs\QueryDTO;
use Mdhesari\LaravelQueryFilters\LaravelQueryFiltersServiceProvider;
use Mdhesari\LaravelQueryFilters\Contracts\Expandable;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Exceptions\MissingCastTypeException;

class ApplyQueryFilters
{
    /**
     * @throws CastTargetException
     * @throws MissingCastTypeException
     * @throws ValidationException
     */
    public function __invoke($query, array $params)
    {
        $params = (new QueryDTO($params))->toArray();

        if ( $query->getModel()->usesTimestamps() ) {
            if ( isset($params['oldest']) && $params['oldest'] ) {
                $query->oldest();
            } else {
                $query->latest();
            }

            if ( isset($params['date_from']) || isset($params['date_to']) ) {
                $query->whereBetween('created_at', [$this->getFromDate($params), $this->getToDate($params)]);
            }
        }

        if ( isset($params['s']) ) {
            $query->searchLike($this->getSearchParams($query), $params['s']);
        }

        if ( isset($params['per_page']) ) {
            $query->getModel()->setPerPage($params['per_page']);
        }

        if ( isset($params['user_id']) && $this->hasUserId($query->getModel()->getFillable()) ) {
            $query->whereUserId($params['user_id']);
        }

        if ( isset($params['expand']) && method_exists($query->getModel(), 'getExpandRelations') ) {
            $relations = $query->getModel()->getExpandRelations();

            $params['expand'] = explode(',', $params['expand']);

            $query->with($this->getOnlyValidRelations($params['expand'], $relations));
        }

        if ( isset($params['order_by']) ) {
            $orderBy = array_filter(explode(',', $params['order_by']), fn($item) => ! empty($item));

            $query->getQuery()->orders = null;

            foreach ($orderBy as $key => $value) {
                if ( ! in_array($value, $this->getSortings()) ) {
                    $sort = isset($orderBy[$key + 1]) && in_array($orderBy[$key + 1], $this->getSortings()) ? $orderBy[$key + 1] : 'desc';

                    $value = explode('.', $value);

                    if ( ! isset($value[1]) )
                        $query->orderBy($value[0], $sort);
                    else {
                        $relationTableName = Str::plural($value[0]);
                        $relationForeignKey = Str::singular($value[0]).'_id';

                        $query->join($relationTableName, "{$relationTableName}.id", '=', "{$query->getModel()->getTable()}.{$relationForeignKey}");

                        $query->orderBy($value[1], $sort);
                    }
                }
            }
        }

        return $query;
    }

    private function getSortings()
    {
        return ['desc', 'asc'];
    }

    private function getSearchParams($query): array
    {
        $model = $query->getModel();

        return Arr::wrap(null ?? method_exists($model, 'getSearchParams') ? $model->getSearchParams() : $model->getRouteKeyName());
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

    private function getOnlyValidRelations(array $expand, $relations)
    {
        return array_filter($expand, fn($expand) => in_array($expand, $relations));
    }
}
