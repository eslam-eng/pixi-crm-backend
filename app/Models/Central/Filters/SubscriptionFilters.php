<?php

namespace App\Models\Central\Filters;

use App\Abstracts\QueryFilter;
use App\Enums\Landlord\SubscriptionStatusEnum;
use Carbon\Carbon;

class SubscriptionFilters extends QueryFilter
{
    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    public function status($status)
    {
        return $this->builder->where('status', $status);
    }

    public function plan_id($plan)
    {
        return $this->builder->where('plan', $plan);
    }

    public function tenant_id($tenant)
    {
        return $this->builder->where('tenant_id', $tenant);
    }

    public function sort_by_latest_active()
    {
        return $this->builder->orderByDesc('id')->orderByDesc('status');
    }

    public function active()
    {
        return $this->builder->where('status', SubscriptionStatusEnum::ACTIVE->value);
    }

    public function created_at($term)
    {
        $dates = explode('to', $term);

        $startDate = trim($dates[0]);

        // If end date is provided, use it; otherwise default to today
        $endDate = isset($dates[1]) && ! empty(trim($dates[1]))
            ? trim($dates[1])
            : Carbon::today()->toDateString();

        return $this->builder->whereBetween('created_at', [$startDate, $endDate]);
    }
}
