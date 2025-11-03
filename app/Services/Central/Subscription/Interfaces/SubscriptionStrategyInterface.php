<?php

namespace App\Services\Central\Subscription\Interfaces;

use App\Models\Central\Invoice;
use App\Models\Tenant\User;

interface SubscriptionStrategyInterface
{
    public function handle(array $params, User $user): ?Invoice;

    public function validate(array $params, User $user): void;

    public function getSubscriptionType(): string;
}
