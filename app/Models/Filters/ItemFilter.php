<?php

namespace App\Models\Filters;

use App\Abstracts\QueryFilter;

class ItemFilter extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function name($term)
    {
        return $this->builder->where('name', "LIKE", "%$term%");
    }

    public function category($term)
    {
        return $this->builder->where('category_id', $term);
    }

    public function type($term)
    {
        return $this->builder->where('itemable_type', $term);
    }

    public function duration($term)
    {
        return $this->builder->where('duration', $term);
    }   
    
    public function service_type($term)
    {
        return $this->builder->whereHas('service', function($query) use ($term) {
            $query->where('service_type', $term);
        });
    }
    
    
}
