<?php

namespace App\Observers;

use App\Models\Tenant\Lead;

class LeadObserver
{
    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        activity()
            ->causedBy(user_id())
            ->performedOn($lead)
            ->withProperties(['attributes' => $lead->getAttributes()])
            ->useLog('lead')
            ->log('lead_created');
    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        $changes = $lead->getDirty();
            if (!empty($changes)) {
                activity()
                    ->causedBy(user_id())
                    ->performedOn($lead)
                    ->withProperties([
                        'old' => $lead->getOriginal(),
                        'attributes' => $changes,
                    ])
                    ->useLog('lead')
                    ->log('lead_updated');
            }
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        activity()
            ->causedBy(user_id())
            ->performedOn($lead)
            ->withProperties(['attributes' => $lead->getAttributes()])
            ->useLog('lead')
            ->log('lead_deleted');
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "force deleted" event.
     */
    public function forceDeleted(Lead $lead): void
    {
        //
    }
}