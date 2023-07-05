<?php

namespace Mdhesari\LaravelQueryFilters;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use PDO;
use Str;
use Arr;

class LaravelQueryFiltersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        Builder::macro('searchWhereHas', function ($query, $attr, $searchQuery, $allAttributes = array()) {

            $attrArr = explode('.', $attr);

            $relation = $attrArr[0];

            $relationAttr = $attrArr[1];

            $whereClause = count($allAttributes) > 1 ? 'orWhereHas' : 'whereHas';

            return $query->{$whereClause}($relation, function (Builder $query) use ($relationAttr, $searchQuery, $attr, $attrArr, $relation, $allAttributes) {

                if (isset($attrArr[2])) {

                    $attr = [$attrArr[1].".".$attrArr[2]];

                    return $query->searchWhereHas($query, $attr, $searchQuery, $allAttributes);
                }

                return $query->where($relationAttr, 'Like', $searchQuery);
            });
        });

        Builder::macro('searchLike', function ($attributes, $searchQuery) {
            $attributes = Arr::wrap($attributes);
            if (count($attributes) > 1) {
                $this->where(function ($query) use ($attributes, $searchQuery) {
                    $query->searchLikeExecute($attributes, $searchQuery);
                });
            } else {
                $this->searchLikeExecute($attributes, $searchQuery);
            }

            return $this;
        });

        Builder::macro('searchLikeExecute', function ($attributes, $searchQuery) {
            foreach (Arr::wrap($attributes) as $attr) {
                if (Str::contains($attr, '.')) {
                    $this->searchWhereHas($this, $attr, "%{$searchQuery}%", $attributes);
                } else {
                    if (count(Arr::wrap($attributes)) > 1) {
                        $this->orWhere($attr, 'Like', "%{$searchQuery}%");
                    } else {
                        $this->where($attr, 'Like', "%{$searchQuery}%");
                    }
                }
            }
        });
    }
}
