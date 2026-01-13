<?php

namespace App\QueryFilters\Central;

use App\Abstracts\QueryFilter;

class CityFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }
    public function name($term)
    {
        return $this->builder->where('name', 'like', "%$term%");
    }

    public function country_id($term)
    {
        return $this->builder->where('country_id', $term);
    }

}
