<?php

namespace App\QueryFilters;

use App\Enums\AwbStatuses;
use Illuminate\Support\Arr;
use App\Abstracts\QueryFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class AwbFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function start_date($term)
    {
        return $this->builder->where('created_at', '>=', $term);
    }

    public function end_date($term)
    {
        return $this->builder->where('created_at', '<=', $term);
    }

    public function start_time($term)
    {
        return $this->builder->whereTime('created_at', '>=', $term);
    }

    public function end_time($term)
    {
        return $this->builder->whereTime('created_at', '<=', $term);
    }

    public function creator_id($term)
    {
        return $this->builder->where('user_id', $term);
    }

    public function id($term)
    {
        return $this->builder->where('id', $term);
    }

    public function code($term)
    {
        return $this->builder->where('code', "LIKE", "%$term%");
    }

    public function keyword($term)
    {
        return $this->builder->where('code', 'LIKE', "%{$term}%")->orWhere('receiver_reference', 'LIKE', "%{$term}%")->orWhere('receiver_data', 'LIKE', "%{$term}%");
    }

    public function status_id($term)
    {
        return $this->builder->whereHas('latestStatus', function ($query) use ($term) {
            $query->where('awb_status_id', $term);
        });
    }

    public function city_id($term)
    {
        return $this->builder->where('receiver_city_id', $term);
    }

    public function area_id($term)
    {
        return $this->builder->where('receiver_area_id', $term);
    }

    public function ids($term)
    {
        return $this->builder->whereIn('id', Arr::wrap($term));
    }

    public function reference($term)
    {
        return $this->builder->where('receiver_reference', $term);
    }

    public function company_id($term)
    {
        return $this->builder->where('company_id', $term);
    }

    public function branch_id($term)
    {
        return $this->builder->where('branch_id', $term);
    }

    public function department_id($term)
    {
        return $this->builder->where('department_id', $term);
    }

    public function receiver_id($term)
    {
        return $this->builder->where('receiver_id', $term);
    }

    public function created_at($term)
    {
        return $this->builder->whereBetween(DB::raw('DATE(created_at)'), $term);
    }

    public function geid($term)
    {
        return $this->builder->whereHas('user', function ($query) use ($term) {
            $query->where('geid', "LIKE", "%$term%");
        });
    }

    public function forCourier($term)
    {
        return $this->builder->where('courier_id', $term);
    }

    public function forPickup($term)
    {
        return $this->builder->where('pickup_courier_id', $term);
    }

    // public function positionCode($term)
    // {
    //     return $this->builder->whereHas('latestStatus', function (Builder $query) {
    //         $query->whereHas('status', function (Builder $query) {
    //             $query->where(function ($q) {
    //                 $q->whereIn('code', [AwbStatuses::OFFICER->value, AwbStatuses::COURIER->value]);
    //             });
    //         });
    //     });
    // }

    public function invoice_id($term = null)
    {
        if ($term === 'null' || $term === null) {
            return $this->builder->whereNull('invoice_id');
        }
        return $this->builder->where('invoice_id', $term);
    }
}
