<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;

class LocationFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function title($term)
    {
        return $this->builder->where('name', "LIKE", "%$term%");
    }

    public function location_id($term)
    {
        return $this->builder->where('id', $term);
    }

    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    public function depth($term)
    {
        return $this->builder->withDepth()->having('depth', $term);
    }

    public function depthlessthen($term)
    {
        return $this->builder->withDepth()->having('depth', '<', $term);
    }

    public function parent($term)
    {
        return $this->builder->where('parent_id', '=', $term);
    }

    public function root()
    {
        return $this->builder->whereIsRoot();
    }

    public function grandParent($term)
    {
        return $this->builder->where('parent_id', '>', $term);
    }
}
