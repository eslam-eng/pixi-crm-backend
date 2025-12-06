<?php

namespace App\Models\Filters;

use App\Abstracts\QueryFilter;
use App\Enums\DurationUnits;
use App\Enums\PermissionsEnum;
use Illuminate\Support\Arr;

class OpportunityFilter extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function status($term)
    {
        return $this->builder->where('status', $term);
    }

    public function assigned_to_id($term)
    {
        return $this->builder->where('assigned_to_id', $term);
    }

    public function assigned_to_users(array $term)
    {
        return $this->builder->whereIn('assigned_to_id', $term);
    }

    public function stage_id($term)
    {
        return $this->builder->where('stage_id', $term);
    }

    public function pipeline_id($term)
    {
        return $this->builder->whereHas('stage', function ($query) use ($term) {
            $query->where('pipeline_id', $term);
        });
    }

    public function source_id($term)
    {
        return $this->builder->whereHas('contact', function ($query) use ($term) {
            $query->where('source_id', $term);
        });
    }

    public function deal_value($term)
    {
        return $this->builder->where('deal_value', $term);
    }

    public function win_probability($term)
    {
        return $this->builder->where('win_probability', $term);
    }

    public function expected_close_date($term)
    {
        return $this->builder->where('expected_close_date', $term);
    }

    public function notes($term)
    {
        return $this->builder->where('notes', $term);
    }

    public function description($term)
    {
        return $this->builder->where('description', $term);
    }

    public function dashboard_view($term)
    {
        $user = $term;

        if ($user->hasPermissionTo(PermissionsEnum::VIEW_ADMIN_DASHBOARD->value)) {
            return $this->builder;
        }

        if ($user->hasPermissionTo(PermissionsEnum::VIEW_MANAGER_DASHBOARD->value)) {
            $teamId = $user->teamManager()->value('id');
            return $this->builder
                ->whereHas('user', function ($query) use ($teamId) {
                    $query->where('team_id', $teamId);
                });
        }

        if ($user->hasPermissionTo(PermissionsEnum::VIEW_AGENT_DASHBOARD->value)) {
            return $this->builder->where('assigned_to_id', $user->id);
        }

         return $this->builder->where('assigned_to_id', $user->id);
    }
}
