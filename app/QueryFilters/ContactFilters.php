<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;

class ContactFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function search($term)
    {
        return $this->builder->where(function ($q) use ($term) {
            $q->where('email', 'LIKE', "%{$term}%")
                ->orWhereHas('contactPhones', function ($qq) use ($term) {
                    $qq->where('phone', 'LIKE', "%{$term}%");
                });
        });
    }

    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    public function source_id($term)
    {
        return $this->builder->where('source_id', $term);
    }

    public function user_id($term)
    {
        return $this->builder->where('user_id', $term);
    }
}
