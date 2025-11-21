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

    public function get_Top_Performing_Sales_Reps($term)
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
