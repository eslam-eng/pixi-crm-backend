<?php

namespace App\Listeners\UserRegistered;

use App\Events\UserRegistered;
use App\Services\Central\Subscription\SubscriptionManager;
use Illuminate\Support\Facades\Log;


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
        Log::info('🎯 [Listener] CreateSubscriptionByActivationCodeListener triggered', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'activation_code' => $event->activation_code,
        ]);
        if (is_null($event->activation_code)) {
            Log::info('⚠️  Skipping subscription creation for user', ['user_id' => $event->user->id]);
            return;
        }
        $user = $event->user;
        $this->subscriptionManager->createActivationCodeSubscription(activationCode: $event->activation_code, user: $user);
    }
}
