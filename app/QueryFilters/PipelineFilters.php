<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;

class PipelineFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function name($term)
    {
        return $this->builder->where('name', "LIKE", "%$term%");
    }
}
