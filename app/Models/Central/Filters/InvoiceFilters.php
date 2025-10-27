<?php

namespace App\Models\Central\Filters;

use App\Abstracts\QueryFilter;
use Carbon\Carbon;

class InvoiceFilters extends QueryFilter
{
    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    public function status($status)
    {
        return $this->builder->where('status', $status);
    }

    public function plan($plan)
    {
        return $this->builder->where('plan', $plan);
    }

    public function tenant($tenant)
    {
        return $this->builder->where('tenant_id', $tenant);
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
