<?php

namespace App\Services\Central\Subscription;

use App\DTO\Central\InvoiceDTO;
use App\DTOs\Landlord\SubscriptionDTO;
use App\Enums\Landlord\InvoiceStatusEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use App\Services\Central\Discount\DiscountCodeService;
use Illuminate\Support\Facades\DB;

readonly class UpgradeSubscriptionService
{
    public function __construct(
        protected CreateSubscriptionService $createSubscriptionService,
        protected DiscountCodeService $discountCodeService) {}

    public function handle(
        Plan $newPlan,
        Tenant $tenant,
        SubscriptionBillingCycleEnum $subscriptionDurationEnum,
        ?string $discount_code = null): Subscription
    {
        return DB::connection('landlord')
            ->transaction(function () use ($tenant, $newPlan, $subscriptionDurationEnum, $discount_code) {
                $currentSubscription = $tenant->activeSubscription;

                $invoiceDTO = $this->prepareInvoiceData(
                    tenant: $tenant,
                    newPlan: $newPlan,
                    currentSubscription: $currentSubscription,
                    subscriptionBillingCycleEnum: $subscriptionDurationEnum,
                    discount_code: $discount_code
                );
                $subscriptionDTO = new SubscriptionDTO(
                    plan_id: $newPlan->id,
                    tenant_id: $tenant->id,
                    starts_at: now(),
                    amount: $invoiceDTO->subtotal,
                    billing_cycle: $subscriptionDurationEnum->value,
                    ends_at: calculateSubscriptionEndDate(duration: $subscriptionDurationEnum),
                    status: SubscriptionStatusEnum::PENDING->value,
                    invoiceDTO: $invoiceDTO,
                    shouldCreateInvoice: true
                );

                return $this->createSubscriptionService->handle(subscriptionDTO: $subscriptionDTO);
            });
    }

    public function prepareInvoiceData(Tenant $tenant, Plan $newPlan, Subscription $currentSubscription, SubscriptionBillingCycleEnum $subscriptionBillingCycleEnum, $discount_code = null)
    {
        $new_subscription_amount = calculateSubscriptionAmount(plan: $newPlan, duration: $subscriptionBillingCycleEnum);

        $proratedAmount = $this->calculateProration(oldSubscription: $currentSubscription, new_subscription_amount: $new_subscription_amount);
        // check discount code
        $discountCode = $this->discountCodeService->validateDiscountForPlan(code: $discount_code, planId: $newPlan->id, tenant: $tenant);
        $finalAmount = $new_subscription_amount - $proratedAmount;
        if ($discountCode) {
            $discountAmount = ($finalAmount * $discountCode->discount_percentage) / 100;
        }

        $invoiceDTO = new InvoiceDTO(
            tenant_id: $tenant->id,
            user_id: auth()->id(),
            subtotal: $new_subscription_amount,
            total: $finalAmount - $discountAmount,
            status: InvoiceStatusEnum::PENDING->value,
            due_date: now(),
            notes: "Upgrade from {$currentSubscription->plan->name} to {$newPlan->name}",
            discountCode: $discountCode,
        );

        $items[] = [
            'description' => $invoiceDTO->notes,
            'quantity' => 1,
            'unit_price' => $invoiceDTO->subtotal,
            'total' => $invoiceDTO->subtotal,
        ];

        if ($proratedAmount > 0) {
            $items[] = [
                'description' => 'Prorated amount',
                'quantity' => 1,
                'unit_price' => $proratedAmount * -1,
                'total' => $proratedAmount * -1,
            ];
        }

        $invoiceDTO->invoiceItems = $items;

        return $invoiceDTO;
    }

    protected function calculateProration(Subscription $oldSubscription, float $new_subscription_amount): float
    {
        $now = now();

        $daysLeft = $now->diffInDays($oldSubscription->ends_at);
        $totalDays = $oldSubscription->starts_at->diffInDays($oldSubscription->ends_at);

        $unusedValue = ($oldSubscription->plan->price / $totalDays) * $daysLeft;
        $newValue = ($new_subscription_amount / $totalDays) * $daysLeft;

        // Positive = charge user, negative = credit
        return round($newValue - $unusedValue, 2);
    }
}
