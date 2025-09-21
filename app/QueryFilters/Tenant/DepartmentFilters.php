<?php

namespace App\QueryFilters\Tenant;

use App\Abstracts\QueryFilter;

class DepartmentFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function name($term)
    {
        return $this->builder->where('name', $term);
    }

    public function description($term)
    {
        return $this->builder->where('description', $term);
    }
 
    public function is_active($term)
    {
        return $this->builder->where('is_active', $term);
    }
}
