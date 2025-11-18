<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;

class LeadFilters extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function name($term)
    {
        return $this->builder->whereRelation('contact.contactPhones', 'phone', $term)->orWhereRelation('contact', 'first_name', "LIKE", "%$term%");
    }

    public function source_id($term)
    {
        return $this->builder->whereRelation('contact', 'source_id', $term);
    }

    public function assigned_to_id($term)
    {
        return $this->builder->where('assigned_to_id', $term);
    }

    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    public function stage_id($term)
    {
        return $this->builder->where('stage_id', $term);
    }

    public function pipeline_id($term)
    {
        return $this->builder->whereRelation('stage', 'pipeline_id', $term);
    }

    public function start_date($term)
    {
        return $this->builder->where('created_at', '>=', $term);
    }

    public function user_id($term)
    {
        return $this->builder->where('assigned_to_id', $term);
    }

    public function end_date($term)
    {
        return $this->builder->where('created_at', '<=', $term);
    }

    public function team_id($term)
    {
        return $this->builder->whereHas('user', function ($query) use ($term) {
            $query->where('team_id', $term);
        });
    }
}
