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
        $params = request()->all();

        if ( isset($params['expand']) && method_exists($model, 'getExpandRelations') ) {
            $relations = $model->getExpandRelations();

            $params['expand'] = explode(',', $params['expand']);

            $query->with(
                $expand = array_filter($params['expand'], fn($expand) => in_array($expand, $relations))
            );

            if ( count($expand) ) {
                request()->request->remove('expand');
            }
        }
    }
}