<?php

namespace App\Models\Central\Filters;

use App\Abstracts\QueryFilter;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ActivationCodeFilters extends QueryFilter
{
    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    public function ids($term)
    {
        return $this->builder->whereIn('id', Arr::wrap($term));
    }

    public function idsNotIn($term)
    {
        return $this->builder->whereNotIn('id', Arr::wrap($term));
    }

    public function source_id($term)
    {
        return $this->builder->where('source_id', $term);
    }

    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    public function plan_id($term)
    {
        return $this->builder->where('plan_id', $term);
    }

    public function tenant_id($term)
    {
        return $this->builder->where('tenant_id', $term);
    }

    public function user_id($term)
    {
        return $this->builder->where('user_id', $term);
    }

    public function redeemed_at($term)
    {
        $dates = explode('to', $term);

        $startDate = trim($dates[0]);

        // If end date is provided, use it; otherwise default to today
        $endDate = isset($dates[1]) && ! empty(trim($dates[1]))
            ? trim($dates[1])
            : Carbon::today()->format('Y-m-d');

        return $this->builder->whereBetween(DB::raw('DATE(redeemed_at)'), [$startDate, $endDate]);

    }
}
