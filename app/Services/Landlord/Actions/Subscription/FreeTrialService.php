<?php

namespace App\Services\Landlord\Actions\Subscription;

use App\DTOs\Landlord\SubscriptionDTO;
use App\Enum\SubscriptionStatusEnum;
use App\Exceptions\TrialException;
use App\Models\Landlord\Tenant;
use App\Services\Landlord\Plan\PlanService;

class FreeTrialService
{
    public function __construct(protected readonly PlanService $planService, protected CreateSubscriptionService $createSubscriptionService) {}

    /**
     * @throws TrialException
     * @throws \Throwable
     */
    public function handle(Tenant $tenant)
    {
        $freePlan = $this->planService->getFreePlan();
        if (! $freePlan) {
            throw new TrialException('there is no plan has trial days ');
        }

        // Check if tenant already had a trial
        $alreadyHadTrial = $tenant->subscriptions()
            ->whereNotNull('trial_ends_at')
            ->exists();

        if ($alreadyHadTrial) {
            throw new TrialException('Tenant already used a trial.');
        }
        $trialEndsAt = now()->addDays($freePlan->trial_days);

        $subscriptionPlanDTO = new SubscriptionDTO(
            plan_id: $freePlan->id,
            tenant_id: $tenant->id,
            starts_at: now(),
            amount: 0,
            trial_ends_at: $trialEndsAt,
            status: SubscriptionStatusEnum::TRIAL->value,
            shouldCreateInvoice: false,
        );

        $this->createSubscriptionService->handle(subscriptionDTO: $subscriptionPlanDTO);
    }
}
