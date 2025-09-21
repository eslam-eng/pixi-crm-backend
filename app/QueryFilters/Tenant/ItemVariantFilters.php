<?php

namespace App\QueryFilters\Tenant;

use Illuminate\Database\Eloquent\Builder;
use App\Abstracts\QueryFilter;

class ItemVariantFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function sku($term)
    {
        return $this->builder->where('sku', 'like', "%{$term}%");
    }

    public function price($term)
    {
        return $this->builder->where('sku', $term);
    }

    public function stock($term)
    {
        return $this->builder->where('sku', $term);
    }

    public function name($term)
    {
        return $this->builder->whereHas('item', function (Builder $query) use ($term) {
            $query->where('name', "LIKE", "%{$term}%");
        });
    }

    public function type($term)
    {
        return $this->builder->whereHas('item', function (Builder $query) use ($term) {
            $query->where('type', $term);
        });
    }

    public function category($term)
    {
        return $this->builder->whereHas('item.category', function (Builder $query) use ($term) {
            $query->where('id', $term);
        });
    }
}
