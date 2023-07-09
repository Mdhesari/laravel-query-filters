<?php

namespace Mdhesari\LaravelQueryFilters\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ExpandScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $query
     * @param Model $model
     * @return void
     */
    public function apply(Builder $query, Model $model)
    {
        $params = request()->input();

        if (isset($params['expand']) && method_exists($model, 'getExpandRelations')) {
            $relations = $model->getExpandRelations();

            if (! is_array($params['expand'])) {
                $params['expand'] = explode(',', $params['expand']);
            }

            $eagerLoads = $query->getEagerLoads();

            $params['expand'] = array_filter($params['expand'], fn ($item) => ! in_array($item, $eagerLoads));

            if (! empty($params['expand'])) {
                $query->with(
                    array_filter($params['expand'], fn ($expand) => in_array($expand, $relations))
                );
            }

            request()->merge(['expand' => $params['expand']]);
        }
    }
}
