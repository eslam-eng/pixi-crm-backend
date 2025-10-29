<?php

namespace App\Listeners\UserRegistered;

use App\Events\UserRegistered;
use App\Services\Landlord\Actions\Subscription\SubscriptionManager;

readonly class CreateFreeTrialListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected SubscriptionManager $subscriptionManager)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event)
    {
        if (! $event->create_free_trial) {
            return;
        }
        $user = $event->user;
        $this->subscriptionManager->createFreeTrialSubscription(user: $user);
    }
}
