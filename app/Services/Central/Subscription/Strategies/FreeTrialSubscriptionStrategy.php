<?php

namespace App\Services\Central\Subscription\Strategies;

use App\DTO\Central\SubscriptionDTO;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Enums\Landlord\SubscriptionTypeEnum;
use App\Models\Central\Invoice;
use App\Models\Central\User;
use Illuminate\Support\Arr;

class FreeTrialSubscriptionStrategy extends AbstractSubscriptionStrategy
{
    public function validate(array $params, User $user): void
    {
        if (! isset($params['plan'])) {
            throw new \InvalidArgumentException('Plan ID is required for free trial');
        }

        $tenant = $user->tenant;
        if ($tenant->hasHadTrial()) {
            throw new \InvalidArgumentException('User already used free trial');
        }
    }

    protected function buildSubscriptionDTO(array $params, User $user): SubscriptionDTO
    {
        $plan = Arr::get($params, 'plan');
        $trialDays = $params['trial_days'] ?? 14;

        // not recommended to use create invoice for free trial
        //        $invoiceDTO = $this->invoiceService->prepareForFreeTrial(
        //            plan: $plan,
        //            user: $user,
        //            trialDays: $trialDays
        //        );

        return new SubscriptionDTO(
            plan_id: $plan->id,
            tenant_id: $user->tenant_id,
            starts_at: now(),
            amount: 0,
            trial_ends_at: now()->addDays($trialDays),
            status: SubscriptionStatusEnum::TRIAL->value,
            //            invoiceDTO: $invoiceDTO,
            shouldCreateInvoice: false
        );
    }

    protected function postProcess(array $params, User $user, ?Invoice $invoice): void
    {
        // Mark user as having used trial
        $tenant = $user->tenant;
        $plan = Arr::get($params, 'plan');
        $tenant->markTrialUsed(planId: $plan->id);
    }

    public function getSubscriptionType(): string
    {
        return SubscriptionTypeEnum::FREE_TRIAL->value;
    }
}
