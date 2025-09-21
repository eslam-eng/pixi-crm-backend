<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;


class ReasonFilters extends QueryFilter
{

    public function name($term)
    {
        return $this->builder->where('name', "LIKE", "%$term%");
    }

    public function start_date($term)
    {
        return $this->builder->whereDate('created_at', '>=', $term);
    }

    public function end_date($term)
    {
        return $this->builder->whereDate('created_at', '<=', $term);
    }
}

