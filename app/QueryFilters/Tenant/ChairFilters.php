<?php

namespace App\QueryFilters\Tenant;

use App\Abstracts\QueryFilter;
use App\Enums\PermissionsEnum;

class ChairFilters extends QueryFilter
{
    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function user($term){
        return $this->builder->where('user_id', $term);
    }

    public function team($term){
        return $this->builder->where('team_id', $term);
    }

    public function chair_rarget($term){
        return $this->builder->whereHas('targets', function($query) use ($term){
            $query->where('year', $term['year'])->where('period_number', $term['period_number']);
        });
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
                ->whereHas('assigned_to', function ($query) use ($teamId) {
                    $query->where('team_id', $teamId);
                });
        }
    }
}
