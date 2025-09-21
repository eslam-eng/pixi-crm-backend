<?php

namespace App\Models\Filters;

use App\Abstracts\QueryFilter;

class ItemCategoryFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function type($term)
    {
        return $this->builder->where('type', $term);
    }

    public function parent_id($term)
    {
        return $this->builder->where('parent_id', $term);
    }
}
