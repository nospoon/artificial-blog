<?php

namespace App\Traits;

use App\Filters\QueryFilters;
use Illuminate\Database\Eloquent\Builder;

trait HasQueryFilters
{
    /**
     * Adds scope to the model
     *
     * @param Builder $query
     * @param QueryFilter $filter
     * @return Builder
     */
    public function scopeFilter(Builder $query, QueryFilters $filter)
    {
        return $filter->apply($query);
    }
}
