<?php

namespace App\Models\Tenant\Filters;

use App\Abstracts\QueryFilter;
use Illuminate\Support\Arr;

class UsersFilter extends QueryFilter
{
    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    public function ids($term)
    {
        return $this->builder->whereIntegerInRaw('id', Arr::wrap($term));
    }

    public function idsNotIn($term)
    {
        return $this->builder->whereIntegerNotInRaw('id', Arr::wrap($term));
    }

    public function email($term)
    {
        return $this->builder->where('email', $term);
    }

    public function phone($term)
    {
        return $this->builder->where('phone', $term);
    }

    public function username($term)
    {
        return $this->builder->where('username', $term);
    }

    public function usernameIn($term)
    {
        return $this->builder->whereIn('username', Arr::wrap($term));
    }

    public function usernameLike($term)
    {
        return $this->builder->where('username', 'LIKE', "%$term%");
    }

    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    public function country($term)
    {
        return $this->builder->where('country', $term);
    }
}
