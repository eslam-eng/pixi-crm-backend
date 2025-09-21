<?php

namespace App\QueryFilters\Tenant;

use App\Abstracts\QueryFilter;

class DealsFilter extends QueryFilter
{
    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    /**
     * Search deals by title, company, or contact
     */
    public function search($term)
    {
        return $this->builder->where(function ($query) use ($term) {
            $query->where('deal_name', 'LIKE', "%$term%")
                ->orWhereHas('contact', function ($q) use ($term) {
                    $q->where('first_name', 'LIKE', "%$term%")
                        ->orWhere('last_name', 'LIKE', "%$term%")
                        ->orWhere('company_name', 'LIKE', "%$term%");
                });
        });
    }

    /**
     * Filter by stage
     */
    public function stage_id($term)
    {
        return $this->builder->where('stage_id', $term);
    }

    /**
     * Filter by assigned user
     */
    public function assigned_to_id($term)
    {
        return $this->builder->where('assigned_to_id', $term);
    }

    /**
     * Filter by source through contact relationship
     */
    public function source_id($term)
    {
        return $this->builder->whereHas('contact', function ($query) use ($term) {
            $query->where('source_id', $term);
        });
    }

    /**
     * Filter by value range (total_amount)
     */
    public function value_range($term)
    {
        // Expect one of: all | small | medium | large
        $value = is_string($term) ? strtolower(trim($term)) : '';

        // Thresholds in the same currency unit as total_amount
        $smallMax = 50000;    // < 50k
        $mediumMin = 50000;   // >= 50k
        $mediumMax = 100000;  // <= 100k
        $largeMin = 100000;   // > 100k

        switch ($value) {
            case 'small':
                return $this->builder->where('total_amount', '<', $smallMax);
            case 'medium':
                return $this->builder
                    ->where('total_amount', '>=', $mediumMin)
                    ->where('total_amount', '<=', $mediumMax);
            case 'large':
                return $this->builder->where('total_amount', '>', $largeMin);
            case 'all':
            default:
                return $this->builder; // no filtering
        }
    }

    /**
     * Filter by created date range
     */
    public function created_date_range($term)
    {
        if (is_array($term)) {
            if (isset($term['start']) && !empty($term['start'])) {
                $this->builder->where('created_at', '>=', $term['start']);
            }
            if (isset($term['end']) && !empty($term['end'])) {
                $this->builder->where('created_at', '<=', $term['end']);
            }
        } elseif (is_string($term) && !empty($term)) {
            // Handle single date string
            $this->builder->where('created_at', '>=', $term);
        }

        return $this->builder;
    }

    /**
     * Filter by sale date range
     */
    public function sale_date_range($term)
    {
        if (is_array($term)) {
            if (isset($term['start']) && !empty($term['start'])) {
                $this->builder->where('sale_date', '>=', $term['start']);
            }
            if (isset($term['end']) && !empty($term['end'])) {
                $this->builder->where('sale_date', '<=', $term['end']);
            }
        } elseif (is_string($term) && !empty($term)) {
            // Handle single date string
            $this->builder->where('sale_date', '>=', $term);
        }

        return $this->builder;
    }

    /**
     * Filter by payment status
     */
    public function payment_status($term)
    {
        return $this->builder->where('payment_status', $term);
    }

    /**
     * Filter by deal type
     */
    public function deal_type($term)
    {
        return $this->builder->where('deal_type', $term);
    }

    /**
     * Filter by multiple stages
     */
    public function stage_ids($term)
    {
        if (is_array($term)) {
            return $this->builder->whereIn('stage_id', $term);
        }

        return $this->builder->where('stage_id', $term);
    }

    /**
     * Filter by multiple assigned users
     */
    public function assigned_to_ids($term)
    {
        if (is_array($term)) {
            return $this->builder->whereIn('assigned_to_id', $term);
        }

        return $this->builder->where('assigned_to_id', $term);
    }

    /**
     * Filter by multiple sources
     */
    public function source_ids($term)
    {
        if (is_array($term)) {
            return $this->builder->whereHas('contact', function ($query) use ($term) {
                $query->whereIn('source_id', $term);
            });
        }

        return $this->builder->whereHas('contact', function ($query) use ($term) {
            $query->where('source_id', $term);
        });
    }
}
