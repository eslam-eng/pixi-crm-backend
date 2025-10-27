<?php

namespace App\Models\Central\Filters;

use App\Abstracts\QueryFilter;

class PlanFilters extends QueryFilter
{
    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    public function monthly_price()
    {
        return $this->builder->whereNotNull('monthly_price')->where('monthly_price', '>', 0);

    }

    public function annual_price()
    {
        return $this->builder->whereNotNull('annual_price')->where('annual_price', '>', 0);
    }

    public function lifetime_price()
    {
        return $this->builder->whereNotNull('lifetime_price')->where('lifetime_price', '>', 0);

    }
}
