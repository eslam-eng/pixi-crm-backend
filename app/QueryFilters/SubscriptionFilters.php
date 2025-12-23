<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionFilters extends QueryFilter
{
    public function search($term)
    {
        return $this->builder->where(function (Builder $query) use ($term) {
            $query->where('subscription_number', 'like', "%{$term}%")
                ->orWhereHas('tenant', function (Builder $q) use ($term) {
                    $q->where('id', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%");
                });
        });
    }

    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    public function plan_id($term)
    {
        return $this->builder->where('plan_id', $term);
    }

    public function activation_method($term)
    {
        return $this->builder->where('activation_method', $term);
    }
}
