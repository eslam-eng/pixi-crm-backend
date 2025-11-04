<?php

namespace App\Models\Central\Filters;

use App\Abstracts\QueryFilter;
use Illuminate\Support\Arr;

class FeaturesFilter extends QueryFilter
{
    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    public function ids($term)
    {
        return $this->builder->whereIntegerInRaw('id', Arr::wrap($term));
    }

    public function idsNotIn($term)
    {
        return $this->builder->whereIntegerNotInRaw('id', Arr::wrap($term));
    }

    public function is_active()
    {
        return $this->builder->where('is_active', true);
    }

    public function group($term)
    {
        return $this->builder->where('group', $term);
    }
}
