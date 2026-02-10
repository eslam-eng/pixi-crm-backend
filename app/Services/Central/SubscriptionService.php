<?php

namespace App\Services\Central;

use App\Enums\Landlord\ActivationMethodEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionPaymentStatusEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Enums\Landlord\TenantStatusEnum;
use App\Models\Central\ActivationCode;
use App\Models\Central\Tenant;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\QueryFilters\SubscriptionFilters;
use App\Services\Central\ActivationCode\ActivationCodeService;
use App\Services\Central\Discount\DiscountCodeService;
use App\Services\Central\Invoice\InvoiceService;
use App\DTO\Central\FeatureSubscriptionDTO;
use App\Services\Central\FeatureSubscriptionService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubscriptionService extends BaseService
{
    public function __construct(
        protected ActivationCodeService $activationCodeService,
        protected DiscountCodeService $discountCodeService,
        protected InvoiceService $invoiceService,
        protected FeatureSubscriptionService $featureSubscriptionService
    ) {
    }

    public function getModel()
    {
        return Subscription::class;
    }

    protected function getFilterClass(): ?string
    {
        return SubscriptionFilters::class;
    }

    protected function baseQuery(): Builder
    {
        return Subscription::query();
    }

    public function store(array $data, ?UploadedFile $file = null): Subscription
    {
        if ($file) {
            $data['file'] = $file->store('subscriptions', 'public');
        }

        if ($data['activation_method'] === ActivationMethodEnum::ACTIVATION_CODE->value) {
            $activationCode = $this->activationCodeService->checkActivationCode($data['activation_code']);
            if (!$activationCode || !$activationCode->plan_id) {
                throw new Exception('Invalid or expired activation code.');
            }
            $data['plan_id'] = $activationCode->plan_id;
        }

        if (isset($data['discount_code']) && !empty($data['discount_code'])) {
            $tenant = Tenant::find($data['tenant_id']);
            $this->discountCodeService->validateDiscountForPlan($data['discount_code'], $data['plan_id'], $tenant);
        }

        $plan = Plan::findOrFail($data['plan_id']);

        $data['amount'] = match ($data['billing_cycle']) {
            SubscriptionBillingCycleEnum::MONTHLY->value => $plan->monthly_price,
            SubscriptionBillingCycleEnum::ANNUAL->value => $plan->annual_price,
            SubscriptionBillingCycleEnum::LIFETIME->value => $plan->lifetime_price,
            default => 0,
        };

        // Plan snapshot logic
        $planSnapshot = $plan->only($plan->getFillable());
        if (method_exists($plan, 'getTranslations')) {
            $planSnapshot['name'] = $plan->getTranslations('name');
        } else {
            $planSnapshot['name'] = $plan->name;
        }

        $data['plan_snapshot'] = $planSnapshot;

        // Create subscription
        $subscription = DB::connection('landlord')->transaction(function () use ($data, $plan) {

            $data['status'] = SubscriptionStatusEnum::ACTIVE->value;
            $data['payment_status'] = SubscriptionPaymentStatusEnum::PAID->value;

            // Ensure starts_at is set if not provided
            if (!isset($data['starts_at'])) {
                $data['starts_at'] = now();
            }

            // Calculate ends_at based on billing_cycle
            if (!isset($data['ends_at'])) {
                $data['ends_at'] = calculateSubscriptionEndDate($data['billing_cycle'], $data['starts_at']);
            }

            $subscription = Subscription::create($data);

            // Create feature subscriptions
            $featureSubscriptions = $plan->features->map(function ($feature) use ($subscription) {
                return new FeatureSubscriptionDTO(
                    subscription_id: $subscription->id,
                    feature_id: $feature->id,
                    slug: $feature->slug,
                    name: method_exists($feature, 'getTranslations') ? $feature->getTranslations('name') : $feature->name,
                    group: $feature->group,
                    value: $feature->pivot->value ?? null,
                    usage: 0,
                );
            })->toArray();

            if (!empty($featureSubscriptions)) {
                $this->featureSubscriptionService->createMany($featureSubscriptions);
            }

            // Create initial invoice
            $this->invoiceService->createFromSubscription(
                $subscription,
                $data['invoice_notes'] ?? null,
                $data['invoice_status'] ?? null,
                $data['payment_method'] ?? null
            );

            return $subscription;
        });

        return $subscription;
    }

    public function renewManual(Subscription|string $subscription, ?string $discountCode = null): Subscription
    {
        if (is_string($subscription)) {
            $subscription = $this->findById($subscription);
        }

        return DB::transaction(function () use ($subscription, $discountCode) {
            $plan = $subscription->plan;

            // Calculate new dates
            $endsAt = $subscription->ends_at ? \Illuminate\Support\Carbon::parse($subscription->ends_at) : null;
            $newStartsAt = $endsAt && $endsAt->isFuture()
                ? $endsAt
                : now();

            $newEndsAt = calculateSubscriptionEndDate($subscription->billing_cycle, $newStartsAt);
            $amount = calculateSubscriptionAmount($plan, $subscription->billing_cycle);

            // Update subscription
            $subscription->update([
                'starts_at' => $newStartsAt,
                'ends_at' => $newEndsAt,
                'amount' => $amount,
                'status' => SubscriptionStatusEnum::ACTIVE->value,
            ]);

            // Create renewal invoice
            $this->invoiceService->createFromSubscription($subscription, "Manual renewal for {$subscription->plan_name}");

            // Activate tenant
            $subscription->tenant->update(['status' => TenantStatusEnum::ACTIVE]);

            return $subscription;
        });
    }

    public function batchAutoRenew(): int
    {
        $subscriptions = Subscription::query()
            ->where('auto_renew', true)
            ->where('status', SubscriptionStatusEnum::ACTIVE)
            ->whereDate('ends_at', now()->toDateString())
            ->get();

        $count = 0;
        foreach ($subscriptions as $subscription) {
            try {
                $this->renewManual($subscription);
                $count++;
            } catch (Exception $e) {
                logger()->error("Auto-renew failed for subscription {$subscription->id}: " . $e->getMessage());
            }
        }

        return $count;
    }

    public function statics()
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Total Subscriptions
        $totalSubscriptions = Subscription::count();
        $lastMonthTotalSubscriptions = Subscription::where('created_at', '<', $startOfMonth)->count();
        $totalSubscriptionsGrowth = $lastMonthTotalSubscriptions > 0
            ? (($totalSubscriptions - $lastMonthTotalSubscriptions) / $lastMonthTotalSubscriptions) * 100
            : ($totalSubscriptions > 0 ? 100 : 0);

        // Active Subscriptions
        $activeSubscriptions = Subscription::where('status', SubscriptionStatusEnum::ACTIVE->value)->count();
        $lastMonthActiveSubscriptions = Subscription::where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->where('created_at', '<', $startOfMonth)
            ->count();
        $activeSubscriptionsGrowth = $lastMonthActiveSubscriptions > 0
            ? (($activeSubscriptions - $lastMonthActiveSubscriptions) / $lastMonthActiveSubscriptions) * 100
            : ($activeSubscriptions > 0 ? 100 : 0);

        // Monthly Revenue (MRR)
        $monthlyRevenue = Subscription::where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->get()
            ->sum(function ($sub) {
                return match ($sub->billing_cycle) {
                    SubscriptionBillingCycleEnum::MONTHLY => (float) $sub->amount,
                    SubscriptionBillingCycleEnum::ANNUAL => (float) $sub->amount / 12,
                    SubscriptionBillingCycleEnum::LIFETIME => (float) $sub->amount / 36, // Assumption
                    default => 0,
                };
            });

        // Simplified Last Month MRR (checking subscriptions that existed at the end of last month)
        $lastMonthMonthlyRevenue = Subscription::where('created_at', '<', $startOfMonth)
            ->where(function ($q) use ($startOfMonth) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $startOfMonth);
            })
            ->get()
            ->sum(function ($sub) {
                return match ($sub->billing_cycle) {
                    SubscriptionBillingCycleEnum::MONTHLY => (float) $sub->amount,
                    SubscriptionBillingCycleEnum::ANNUAL => (float) $sub->amount / 12,
                    SubscriptionBillingCycleEnum::LIFETIME => (float) $sub->amount / 36,
                    default => 0,
                };
            });

        $monthlyRevenueGrowth = $lastMonthMonthlyRevenue > 0
            ? (($monthlyRevenue - $lastMonthMonthlyRevenue) / $lastMonthMonthlyRevenue) * 100
            : ($monthlyRevenue > 0 ? 100 : 0);

        // Renewal Rate (Simplified: active / (active + expired) in the last 30 days)
        // Or strictly: (Number of renewals) / (Number of expected renewals)
        // For UI purposes, let's use a simpler heuristic if we don't have a renewal log.
        // Let's assume renewal rate is related to subscriptions that were due to end and stayed active.

        $dueToRenew = Subscription::whereBetween('ends_at', [$now->copy()->subDays(30), $now])->count();
        $renewed = Subscription::whereBetween('ends_at', [$now->copy()->subDays(30), $now])
            ->where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->count();

        $renewalRate = $dueToRenew > 0 ? ($renewed / $dueToRenew) * 100 : 0;

        // Last 30-60 days for comparison
        $dueToRenewLast = Subscription::whereBetween('ends_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])->count();
        $renewedLast = Subscription::whereBetween('ends_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])
            ->where('status', SubscriptionStatusEnum::ACTIVE->value)
            ->count();
        $renewalRateLast = $dueToRenewLast > 0 ? ($renewedLast / $dueToRenewLast) * 100 : 0;

        $renewalRateGrowth = $renewalRate - $renewalRateLast; // Point change instead of percentage change for rates is common

        return [
            'total_subscriptions' => [
                'value' => $totalSubscriptions,
                'growth' => round($totalSubscriptionsGrowth, 2),
            ],
            'active_subscriptions' => [
                'value' => $activeSubscriptions,
                'growth' => round($activeSubscriptionsGrowth, 2),
            ],
            'monthly_revenue' => [
                'value' => round($monthlyRevenue, 2),
                'growth' => round($monthlyRevenueGrowth, 2),
            ],
            'renewal_rate' => [
                'value' => round($renewalRate, 2),
                'growth' => round($renewalRateGrowth, 2),
            ]
        ];
    }
}
