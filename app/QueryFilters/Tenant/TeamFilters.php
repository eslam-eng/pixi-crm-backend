<?php

namespace App\QueryFilters\Tenant;

use App\Abstracts\QueryFilter;

class TeamFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function is_target($term)
    {
        return $this->builder->where('is_target', $term);
    }
}
