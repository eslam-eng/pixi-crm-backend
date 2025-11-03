<?php

namespace App\Listeners\UserRegistered;

use App\Events\UserRegistered;
use App\Services\Central\Subscription\SubscriptionManager;
use Illuminate\Support\Facades\Log;


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
        Log::info('🎯 [Listener] CreateFreeTrialListener triggered', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'create_free_trial' => $event->create_free_trial,
        ]);
        if (! $event->create_free_trial) {
            Log::info('⚠️  Skipping free trial creation for user', ['user_id' => $event->user->id]);
            return;
        }
        $user = $event->user;
        try {
            $this->subscriptionManager->createFreeTrialSubscription(user: $user);
            Log::info('✅ Free trial subscription created successfully', [
                'user_id' => $event->user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error creating free trial subscription', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function failed(UserRegistered $event, \Throwable $exception): void
    {
        Log::error('💥 Queued listener failed', [
            'user_id' => $event->user->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
