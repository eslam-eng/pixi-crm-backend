<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;
use Arr;

class PlanFilters extends QueryFilter
{
    public function __construct($params = [])
    {
        parent::__construct($params);
    }

    public function is_trial($term)
    {
        return $this->builder->where('is_trial', $term);
    }

    public function monthly_price()
    {
        return $this->builder->whereNotNull('monthly_price')->where('monthly_price', '>', 0);

    }

    public function annual_price()
    {
        return $this->builder->whereNotNull('annual_price')->where('annual_price', '>', 0);
    }

    public function lifetime_price()
    {
        return $this->builder->whereNotNull('lifetime_price')->where('lifetime_price', '>', 0);

    }
    public function ids($term)
    {
        return $this->builder->whereIntegerInRaw('id', Arr::wrap($term));
    }

    public function package_name($term)
    {
        return $this->builder->where('package_name', 'LIKE', "%$term%");
    }

    public function description($term)
    {
        return $this->builder->where('description', 'LIKE', "%$term%");
    }


    public function refund_days($term)
    {
        return $this->builder->where('refund_days', $term);
    }
    public function min_users($users)
    {
        return $this->builder->where('max_users', '>=', $users);
    }


    public function max_users($users)
    {
        return $this->builder->where('max_users', '<=', $users);
    }

    public function max_users_range($range)
    {
        [$min, $max] = explode(',', $range);
        return $this->builder->whereBetween('max_users', [$min, $max]);
    }

    public function min_max_contacts($contacts)
    {
        return $this->builder->where('max_contacts', '>=', $contacts);
    }

    public function max_max_contacts($contacts)
    {
        return $this->builder->where('max_contacts', '<=', $contacts);
    }

    public function max_contacts_range($range)
    {
        [$min, $max] = explode(',', $range);
        return $this->builder->whereBetween('max_contacts', [$min, $max]);
    }

    public function min_storage_limit($storage)
    {
        return $this->builder->where('storage_limit', '>=', $storage);
    }

    public function max_storage_limit($storage)
    {
        return $this->builder->where('storage_limit', '<=', $storage);
    }

    public function storage_limit_range($range)
    {
        [$min, $max] = explode(',', $range);
        return $this->builder->whereBetween('storage_limit', [$min, $max]);
    }

    public function status($term)
    {
        $validStatuses = ['active', 'inactive'];
        if (in_array($term, $validStatuses)) {
            return $this->builder->where('status', $term);
        }
        return $this->builder;
    }

    public function availability($term)
    {
        $validAvailabilities = ['Public', 'Private'];
        if (in_array($term, $validAvailabilities)) {
            return $this->builder->where('availability', $term);
        }
        return $this->builder;
    }

    public function has_module($module)
    {
        return $this->builder->whereJsonContains('modules', $module);
    }

    public function module_id($term)
    {
        return $this->builder->whereHas('tier_modules', function ($query) use ($term) {
            $query->where('module_id', $term);
        });
    }


    public function after_date($date)
    {
        return $this->builder->where('created_at', '>=', $date);
    }

    public function before_date($date)
    {
        return $this->builder->where('created_at', '<=', $date);
    }

    public function created_after($date)
    {
        return $this->builder->whereDate('created_at', '>=', $date);
    }

    public function created_before($date)
    {
        return $this->builder->whereDate('created_at', '<=', $date);
    }

    public function created_between($range)
    {
        [$start, $end] = explode(',', $range);
        return $this->builder->whereBetween('created_at', [$start, $end]);
    }

    public function updated_after($date)
    {
        return $this->builder->whereDate('updated_at', '>=', $date);
    }

    public function updated_before($date)
    {
        return $this->builder->whereDate('updated_at', '<=', $date);
    }

    public function updated_between($range)
    {
        [$start, $end] = explode(',', $range);
        return $this->builder->whereBetween('updated_at', [$start, $end]);
    }
}
