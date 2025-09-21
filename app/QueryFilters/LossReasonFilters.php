<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;

class LossReasonFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function label($term)
    {
        return $this->builder->where('label', "LIKE", "%$term%");
    }
}
