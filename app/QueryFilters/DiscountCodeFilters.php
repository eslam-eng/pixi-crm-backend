<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;

class DiscountCodeFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function discount_code($term)
    {
        return $this->builder->where('discount_code', "LIKE", "%$term%");
    }

    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    public function plan_id($term)
    {
        return $this->builder->where('plan_id', $term);
    }

    public function discount_type($term)
    {
        return $this->builder->where('discount_type', $term);
    }

    public function using_state($term)
    {
        $result = match ($term) {
            'used' => $this->builder->whereNotNull('used_at'),
            'free' => $this->builder->whereNull('used_at'),
            default => $this->builder
        };
        return $result;
    }
}
