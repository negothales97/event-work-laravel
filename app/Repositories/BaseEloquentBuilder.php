<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

class BaseEloquentBuilder extends Builder
{
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        if (request()->has('per_page')) {
            $perPage = request()->per_page;
        } else {
            $perPage = $perPage ? $perPage : config('repository.pagination.limit', 100);
        }
        return parent::paginate($perPage, $columns, $pageName, $page);
    }
}
