<?php

namespace App\Observers;

use App\Models\Tenant\Deal;

class DealObserver
{
    /**
     * Handle the Deal "created" event.
     */
    public function created(Deal $deal): void
    {
        activity()
            ->performedOn($deal)
            ->causedBy(user_id())
            ->withProperties([
                'attributes' => $deal->getAttributes()
            ])
            ->useLog('deal')
            ->log('deal_created');
    }

    /**
     * Handle the Deal "updated" event.
     */
    public function updated(Deal $deal): void
    {
        //
    }

    /**
     * Handle the Deal "deleted" event.
     */
    public function deleted(Deal $deal): void
    {
        //
    }

    /**
     * Handle the Deal "restored" event.
     */
    public function restored(Deal $deal): void
    {
        //
    }

    /**
     * Handle the Deal "force deleted" event.
     */
    public function forceDeleted(Deal $deal): void
    {
        //
    }
}
