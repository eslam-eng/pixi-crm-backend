<?php

namespace App\Models\Filters;

use App\Abstracts\QueryFilter;
use App\Enums\DurationUnits;
use Illuminate\Support\Arr;

class TierFilter extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
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

    public function price($price)
    {
        return $this->builder->where('price', '<=', $price);
    }

    public function min_price($price)
    {
        return $this->builder->where('price', '>=', $price);
    }

    public function max_price($price)
    {
        return $this->builder->where('price', '<=', $price);
    }

    public function price_range($range)
    {
        [$min, $max] = explode(',', $range);
        return $this->builder->whereBetween('price', [$min, $max]);
    }

    public function duration_unit($term)
    {
        $validUnits = DurationUnits::values();
        if (in_array($term, $validUnits)) {
            return $this->builder->where('duration_unit', $term);
        }
        return $this->builder;
    }

    public function min_duration($duration)
    {
        return $this->builder->where('duration', '>=', $duration);
    }

    public function max_duration($duration)
    {
        return $this->builder->where('duration', '<=', $duration);
    }

    public function duration_range($range)
    {
        [$min, $max] = explode(',', $range);
        return $this->builder->whereBetween('duration', [$min, $max]);
    }

    public function refund_period($term)
    {
        return $this->builder->where('refund_period', $term);
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

    public function price_type($type)
    {
        switch (strtolower($type)) {
            case 'free':
                return $this->builder->where('price', 0);
            case 'low':
                return $this->builder->where('price', '<', 50);
            case 'medium':
                return $this->builder->whereBetween('price', [50, 200]);
            case 'high':
                return $this->builder->whereBetween('price', [200, 500]);
            case 'premium':
                return $this->builder->where('price', '>', 500);
            default:
                return $this->builder;
        }
    }
}
