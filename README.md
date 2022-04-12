# Laravel Query Filters

Cool idea to apply query filters in your controllers.

## Install

```shell script
composer require mdhesari/laravel-query-filters
```

## Usage

```php

public function index(\Illuminate\Http\Request $request, \Mdhesari\LaravelQueryFilters\Actions\ApplyQueryFilters $applyQueryFilters) {
    return $applyQueryFilters(\MyModel::query(), $request->all())->paginate();
}

```

## Actions

Query filters are a bunch of actions that you can use in order to filter your controllers.

## Extend

We have provided some default filters in the default action that you can extend it with its abstract implementation.