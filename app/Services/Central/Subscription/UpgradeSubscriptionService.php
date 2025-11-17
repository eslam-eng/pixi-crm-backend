<?php

namespace App\Services\Central\Subscription;

use App\DTO\Central\InvoiceDTO;
use App\DTO\Central\SubscriptionDTO;
use App\Enums\Landlord\InvoiceStatusEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Models\Central\Invoice;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use App\Services\Central\Discount\DiscountCodeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Instanceof_;

readonly class UpgradeSubscriptionService
{
    public function __construct(
        protected CreateSubscriptionService $createSubscriptionService,
        protected DiscountCodeService $discountCodeService
    ) {}

    public function handle(
        string|int $planId,
        Tenant $tenant,
        SubscriptionBillingCycleEnum $subscriptionDurationEnum,
        ?string $discount_code = null
    ): Invoice {
        return DB::connection('landlord')
            ->transaction(function () use ($tenant, $planId, $subscriptionDurationEnum, $discount_code) {
                $newPlan = Plan::query()->findOrFail($planId);

                /** @var Subscription|null $currentSubscription */
                $currentSubscription = $tenant->activeSubscription;

                if (!$currentSubscription) {
                    throw new \RuntimeException('Cannot upgrade subscription: tenant has no active subscription.');
                }

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

                $invoice = $this->createSubscriptionService->handle(subscriptionDTO: $subscriptionDTO);

                if (!$invoice || !$invoice->subscription_id) {
                    throw new \RuntimeException('Failed to create subscription or invoice.');
                }

                return $invoice;
            });
    }

    public function prepareInvoiceData(Tenant $tenant, Plan $newPlan, Subscription $currentSubscription, SubscriptionBillingCycleEnum $subscriptionBillingCycleEnum, $discount_code = null)
    {
        $new_subscription_amount = calculateSubscriptionAmount(plan: $newPlan, duration: $subscriptionBillingCycleEnum);

        $proratedAmount = $this->calculateProration(oldSubscription: $currentSubscription, new_subscription_amount: $new_subscription_amount);
        // check discount code
        $discountCode = $this->discountCodeService->validateDiscountForPlan(code: $discount_code, planId: $newPlan->id, tenant: $tenant);
        if (!$discountCode) {
            $discountCode = 1;
        }
        $finalAmount = $new_subscription_amount - $proratedAmount;
        if ($discountCode) {
            $discountAmount = ($finalAmount *  $discountCode? $discountCode->discount_percentage :1) / 100;
        }

        $invoiceDTO = new InvoiceDTO(
            tenant_id: $tenant->id,
            user_id: auth()->id(),
            subtotal: $new_subscription_amount,
            total: $finalAmount - $discountAmount??0,
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

        $startsAt = Carbon::parse($oldSubscription->starts_at);
        $endsAt = Carbon::parse($oldSubscription->ends_at);

        $daysLeft = $now->diffInDays($endsAt);
        $totalDays = $startsAt->diffInDays($endsAt);

        // Prevent division by zero
        if ($totalDays <= 0) {
            return 0;
        }

        $unusedValue = ($oldSubscription->plan->price / $totalDays) * $daysLeft;
        $newValue = ($new_subscription_amount / $totalDays) * $daysLeft;

        // Positive = charge user, negative = credit
        return round($newValue - $unusedValue, 2);
    }
}
