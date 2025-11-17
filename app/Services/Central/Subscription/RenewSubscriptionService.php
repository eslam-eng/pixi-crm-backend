<?php

namespace App\Services\Central\Subscription;

use App\DTO\Central\InvoiceDTO;
use App\Enums\Landlord\InvoiceStatusEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Exceptions\DiscountCodeException;
use App\Models\Central\FeatureSubscription;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use App\Services\Central\Discount\DiscountCodeService;
use App\Services\Central\Invoice\InvoiceService;
use Illuminate\Support\Facades\DB;

class RenewSubscriptionService
{
    protected const DEFAULT_FEATURE_USAGE = 0;

    public function __construct(
        protected readonly InvoiceService $invoiceService,
        protected readonly DiscountCodeService $discountCodeService,
    ) {}

    /**
     * @throws \Throwable
     */
    public function handle(Subscription|string $currentSubscription, ?string $discount_code = null): Subscription
    {
        return DB::connection('landlord')
            ->transaction(function () use ($discount_code, $currentSubscription) {
                $currentSubscription = Subscription::findOrFail($currentSubscription);

                $subscriptionDurationEnum = SubscriptionBillingCycleEnum::from($currentSubscription->billing_cycle->value);

                // Get plan snapshot from the current subscription

                $plan = $currentSubscription->plan;

                $tenant = $currentSubscription->tenant;

                $planConfiguration = $this->preparePlanConfiguration(plan: $plan);

                $subscriptionAmount = $this->calculateSubscriptionAmount(plan: $plan, duration: $subscriptionDurationEnum);

                $subscriptionEndDate = $this->calculateSubscriptionEndDate(duration: $subscriptionDurationEnum);

                // Update current subscription
                $currentSubscription->update([
                    'amount' => $subscriptionAmount,
                    'ends_at' => $subscriptionEndDate,
                    'plan_snapshot' => $planConfiguration,
                    'status' => SubscriptionStatusEnum::PENDING->value,
                ]);

                $currentSubscription = $currentSubscription->refresh();

                $this->resetFeatureSubscriptions(plan: $plan, subscription: $currentSubscription);

                $invoiceDTO = $this->prepareInvoiceDTO(tenant: $tenant, newSubscription: $currentSubscription, discount_code: $discount_code);

                return $this->invoiceService->create(invoiceDTO: $invoiceDTO);
            });
    }

    protected function calculateSubscriptionEndDate(SubscriptionBillingCycleEnum $duration): ?\DateTime
    {
        return match ($duration->value) {
            SubscriptionBillingCycleEnum::MONTHLY->value => now()->addMonth(),
            SubscriptionBillingCycleEnum::ANNUAL->value => now()->addYear(),
            SubscriptionBillingCycleEnum::LIFETIME->value => null,
        };
    }

    protected function calculateSubscriptionAmount(Plan $plan, SubscriptionBillingCycleEnum $duration): float
    {
        return match ($duration->value) {
            SubscriptionBillingCycleEnum::MONTHLY->value,
            SubscriptionBillingCycleEnum::ANNUAL->value,
            SubscriptionBillingCycleEnum::LIFETIME->value => $plan->monthly_price,
        };
    }

    protected function preparePlanConfiguration(Plan $plan): array
    {
        $planConfiguration = $plan->only($plan->getFillable());
        $planConfiguration['name'] = $plan->getTranslations('name');

        return $planConfiguration;
    }

    /**
     * @throws DiscountCodeException
     */
    private function prepareInvoiceDTO(Tenant $tenant, Subscription $newSubscription, ?string $discount_code = null): InvoiceDTO
    {
        // check discount code
        // if($discount_code){
            $discountCode = $this->discountCodeService->validateDiscountForPlan(code: $discount_code, planId: $newSubscription->plan_id, tenant: $tenant);
        // }
        
        $discountAmount = 0;
        if ($discountCode) {
            $discountAmount = ($newSubscription->amount * $discountCode->discount_percentage) / 100;
        }
        $invoiceDTO = new InvoiceDTO(
            tenant_id: $tenant->id,
            user_id: auth()->id(),
            subscription_id: $newSubscription->id,
            subtotal: $newSubscription->amount,
            total: $newSubscription->amount - $discountAmount,
            status: InvoiceStatusEnum::PENDING->value,
            due_date: now(),
            notes: "Renewal for plan {$newSubscription->plan->name}",
            discountCode: $discountCode ? $discountCode : null,
        );
        $items[] = [
            'description' => $invoiceDTO->notes,
            'quantity' => 1,
            'unit_price' => $invoiceDTO->subtotal,
            'total' => $invoiceDTO->subtotal,
        ];
        $invoiceDTO->invoiceItems = $items;

        return $invoiceDTO;
    }

    protected function resetFeatureSubscriptions(Plan $plan, Subscription $subscription): void
    {
        $featureSubscriptions = $plan->features->map(function ($feature) use ($subscription) {
            return [
                'subscription_id' => $subscription->id,
                'feature_id' => $feature->id,
                'slug' => $feature->slug,
                'name' => json_encode($feature->getTranslations('name')),
                'group' => $feature->group,
                'value' => $feature->pivot->value,
                'usage' => self::DEFAULT_FEATURE_USAGE,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        if (! empty($featureSubscriptions)) {
            // delete old feature subscriptions
            FeatureSubscription::query()->where('subscription_id', $subscription->id)->delete();

            FeatureSubscription::query()->insert($featureSubscriptions);
        }
    }
}
