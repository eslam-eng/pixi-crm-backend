<?php

namespace App\Services\Central;

use App\Enums\Landlord\ActivationMethodEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Models\Central\ActivationCode;
use App\Models\Central\Tenant;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\QueryFilters\SubscriptionFilters;
use App\Services\Central\ActivationCode\ActivationCodeService;
use App\Services\Central\Discount\DiscountCodeService;
use App\Services\Central\Invoice\InvoiceService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubscriptionService extends BaseService
{
    public function __construct(
        protected ActivationCodeService $activationCodeService,
        protected DiscountCodeService $discountCodeService,
        protected InvoiceService $invoiceService
    ) {
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
        $subscription = DB::transaction(function () use ($data, $plan) {
            $subscription = Subscription::create($data);

            // Create feature subscriptions
            $featureSubscriptions = $plan->features->map(function ($feature) use ($subscription) {
                return [
                    'subscription_id' => $subscription->id,
                    'feature_id' => $feature->id,
                    'slug' => $feature->slug,
                    'name' => method_exists($feature, 'getTranslations') ? json_encode($feature->getTranslations('name')) : json_encode($feature->name),
                    'group' => $feature->group,
                    'value' => $feature->pivot->value ?? null,
                    'usage' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            if (!empty($featureSubscriptions)) {
                DB::table('feature_subscriptions')->insert($featureSubscriptions);
            }

            // Create initial invoice
            $this->invoiceService->createFromSubscription($subscription);

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
}
