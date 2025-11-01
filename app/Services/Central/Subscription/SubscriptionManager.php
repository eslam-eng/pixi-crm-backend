<?php

namespace App\Services\Central\Subscription;

use App\Enum\SubscriptionTypeEnum;
use App\Models\Landlord\Plan;
use App\Models\Landlord\User;
use App\Services\Landlord\Actions\Subscription\Factory\SubscriptionFactory;

class SubscriptionManager
{
    public function __construct(private SubscriptionFactory $subscriptionFactory) {}

    public function handleSubscription(string $type, array $params, User $user)
    {
        $strategy = $this->subscriptionFactory->make($type);

        return $strategy->handle($params, $user);
    }

    public function createPaidSubscription(array $params, User $user)
    {
        return $this->handleSubscription(SubscriptionTypeEnum::PAID->value, $params, $user);
    }

    public function createActivationCodeSubscription(string $activationCode, User $user)
    {
        return $this->handleSubscription(SubscriptionTypeEnum::ACTIVATION_CODE->value, [
            'activation_code' => $activationCode,
        ], $user);
    }

    public function renewSubscription(array $params, User $user): array
    {
        return $this->handleSubscription(SubscriptionTypeEnum::RENEW->value, $params, $user);
    }

    public function createFreeTrialSubscription(User $user, int $trialDays = 14)
    {
        $freePlan = Plan::query()->trial()->first();
        $trialDays = $freePlan->trial_days ?? $trialDays;

        return $this->handleSubscription(SubscriptionTypeEnum::FREE_TRIAL->value, [
            'plan' => $freePlan,
            'trial_days' => $trialDays,
        ], $user);
    }
}
