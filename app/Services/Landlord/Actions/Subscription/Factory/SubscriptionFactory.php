<?php

namespace App\Services\Landlord\Actions\Subscription\Factory;

use App\Enum\SubscriptionTypeEnum;
use App\Services\Landlord\Actions\Subscription\Interfaces\SubscriptionStrategyInterface;
use App\Services\Landlord\Actions\Subscription\Strategies\ActivationCodeSubscriptionStrategy;
use App\Services\Landlord\Actions\Subscription\Strategies\FreeTrialSubscriptionStrategy;
use App\Services\Landlord\Actions\Subscription\Strategies\PaidSubscriptionStrategy;

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
