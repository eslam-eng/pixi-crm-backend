<?php

namespace App\Listeners\UserRegistered;

use App\Events\UserRegistered;
use App\Services\Landlord\Actions\Subscription\SubscriptionManager;

readonly class CreateSubscriptionByActivationCodeListener
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
        if (is_null($event->activation_code)) {
            return;
        }
        $user = $event->user;
        $this->subscriptionManager->createActivationCodeSubscription(activationCode: $event->activation_code, user: $user);
    }
}
