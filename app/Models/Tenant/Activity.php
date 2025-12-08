<?php

namespace App\Models\Tenant;

use App\Services\Tenant\Users\UserService;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    public function scopeVisibleFor($query, $user_id)
    {
        $userService = app(UserService::class);
        $user = $userService->findById($user_id);

        // Admin → See everything
        if ($user->hasRole('admin')) {
            return;
        }

        // Manager → See users in their team
        if ($user->hasRole('manager')) {
            $teamUserIds = $userService->getModel()->where('team_id', $user->team_id)->pluck('id');
            return $query->whereIn('causer_id', $teamUserIds);
        }

        // Agent → See only their own activity
        return $query->where('causer_id', $user->id);
    }

    public function changedValues(): array
    {
        // "properties" is the actual column in Spatie Activity
        $changes = $this->properties['attributes'] ?? [];
        $old = $this->properties['old'] ?? [];

        $result = [];

        foreach ($changes as $key => $newValue) {
            $oldValue = $old[$key] ?? null;

            if ($oldValue !== $newValue) {
                $result[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $result;
    }
}
