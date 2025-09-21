<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;

class ActivationCodeFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function activation_code($term)
    {
        return $this->builder->where('activation_code', "LIKE", "%$term%");
    }

    public function tier_id($term)
    {
        return $this->builder->where('tier_id', $term);
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
