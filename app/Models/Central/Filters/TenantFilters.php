<?php

namespace App\Models\Central\Filters;

use App\Abstracts\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TenantFilters extends QueryFilter
{
    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    public function ids($term)
    {
        return $this->builder->whereIntegerInRaw('id', Arr::wrap($term));
    }

    public function package_id($term)
    {
        return $this->builder->whereHas('activeSubscription', function ($query) use ($term) {
            $query->where('plan_id', $term);
        });
    }

    public function expiration($term): Builder
    {
        return $this->builder->whereHas('activeSubscription', function ($query) use ($term) {
            $query->whereBetween('ends_at', [
                Carbon::today()->format('Y-m-d'),
                Carbon::today()->addDays((int) $term)->format('Y-m-d'),
            ]);
        });
    }

    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    public function subscription_status($term): Builder
    {
        return $this->builder->whereHas('subscriptions', function ($query) use ($term) {
            $query->where('status', $term);
        });
    }
}
