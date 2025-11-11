<?php

namespace App\QueryFilters\Tenant;

use App\Abstracts\QueryFilter;

class UsersFilters extends QueryFilter
{
    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function email($term)
    {
        return $this->builder->where('email', $term);
    }

    public function phone($term)
    {
        return $this->builder->where('phone', $term);
    }

    public function is_active($term)
    {
        return $this->builder->where('is_active', $term);
    }

    public function role($term)
    {
        if (gettype($term) == 'array') {
            return $this->builder->whereHas('roles', function ($query) use ($term) {
                $query->whereIn('name', $term);
            });
        }

        return $this->builder->role($term);
    }

    public function department_id($term)
    {
        return $this->builder->where('department_id', $term);
    }

    public function search($term)
    {
        return $this->builder->where(function ($query) use ($term) {
            $query->where('first_name', 'LIKE', "%{$term}%")
                  ->orWhere('last_name', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%")
                  ->orWhere('phone', 'LIKE', "%{$term}%");
        });
    }

    public function no_team()
    {
        return $this->builder->whereNull('team_id');
    }
}
