<?php

namespace App\Services\Landlord\Actions\Subscription\Interfaces;

use App\Models\Landlord\Invoice;
use App\Models\Landlord\User;

interface SubscriptionStrategyInterface
{
    public function handle(array $params, User $user): ?Invoice;

    public function validate(array $params, User $user): void;

    public function getSubscriptionType(): string;
}
