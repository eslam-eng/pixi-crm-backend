<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;

class CustomFieldFilters extends QueryFilter
{
    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function name($term)
    {
        return $this->builder->where('name', 'LIKE', "%$term%");
    }

    public function type($term)
    {
        return $this->builder->where('type','LIKE', "%$term%");
    }


    public function start_date($term)
    {
        return $this->builder->whereDate('created_at', '>=', $term);
    }

    public function end_date($term)
    {
        return $this->builder->whereDate('created_at', '<=', $term);
    }
}
