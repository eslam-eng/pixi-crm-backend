<?php

namespace App\Services\Landlord\Actions\Subscription\Strategies;

use App\DTOs\Landlord\SubscriptionDTO;
use App\Enum\SubscriptionStatusEnum;
use App\Enum\SubscriptionTypeEnum;
use App\Models\Landlord\Invoice;
use App\Models\Landlord\User;
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
