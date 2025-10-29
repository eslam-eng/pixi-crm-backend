<?php

namespace App\Services\Landlord\Actions\Subscription\Factory;

use App\Enums\Landlord\SubscriptionTypeEnum;
use App\Services\Central\Subscription\Interfaces\SubscriptionStrategyInterface;
use App\Services\Central\Subscription\Strategies\ActivationCodeSubscriptionStrategy;
use App\Services\Central\Subscription\Strategies\FreeTrialSubscriptionStrategy;
use App\Services\Central\Subscription\Strategies\PaidSubscriptionStrategy;

class SubscriptionFactory
{
    public function __construct(
        private PaidSubscriptionStrategy $paidStrategy,
        private ActivationCodeSubscriptionStrategy $activationCodeStrategy,
        private FreeTrialSubscriptionStrategy $freeTrialStrategy,
    ) {}

    public function make(string $type): SubscriptionStrategyInterface
    {
        return match ($type) {
            SubscriptionTypeEnum::PAID->value => $this->paidStrategy,
            SubscriptionTypeEnum::ACTIVATION_CODE->value => $this->activationCodeStrategy,
            SubscriptionTypeEnum::FREE_TRIAL->value => $this->freeTrialStrategy,
            default => throw new \InvalidArgumentException("Unknown subscription type: {$type}")
        };
    }
}
