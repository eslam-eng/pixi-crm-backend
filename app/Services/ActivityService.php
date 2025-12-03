<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;

class ActivityService
{
    /**
     * Get latest 5 activities for a specific user across all models
     */
    public function getUserRecentActivities(int $userId, int $limit = 5)
    {
        return Activity::where('causer_id', null)
            ->orWhere('causer_id', $userId)
            ->with(['subject','causer'])
            ->latest()
            ->limit($limit)
            ->get();
    }


    /**
     * Get activity type for badge display
     */
    protected function getActivityType(Activity $activity): string
    {
        // Use log_name if available
        if ($activity->log_name) {
            return ucfirst($activity->log_name);
        }

        // Otherwise use subject type
        if ($activity->subject_type) {
            return class_basename($activity->subject_type);
        }

        return 'Activity';
    }
}
