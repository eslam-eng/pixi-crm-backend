<?php

namespace App\Services\Central\Subscription;

use App\DTO\Central\SubscriptionDTO;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Models\Central\Filters\SubscriptionFilters;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Services\Central\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubscriptionService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return SubscriptionFilters::class;
    }

    protected function baseQuery(): Builder
    {
        return Subscription::query();
    }

    public function create(SubscriptionDTO $subscriptionPlanDTO)
    {
        $subscriptionData = $subscriptionPlanDTO->toArray();

        if (! empty($subscriptionPlanDTO->plan_snapshot)) {
            $subscriptionData['plan_snapshot'] = json_encode($subscriptionPlanDTO->plan_snapshot);
        } else {
            $plan = Plan::query()->find($subscriptionPlanDTO->plan_id);
            $planSnapshot = $plan->only($plan->getFillable());
            $planSnapshot['name'] = $plan->getTranslations('name');
            $subscriptionData['plan_snapshot'] = json_encode($planSnapshot);
        }

        return $this->getQuery()->create($subscriptionData);
    }

    public function staticsByStatus(): Collection
    {
        return $this->getQuery()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

    }

    public function paginate(?array $filters = [], int $perPage = 15): LengthAwarePaginator
    {

        return $this->getQuery(filters: $filters)
            ->with(['tenant:id,name', 'plan:id,name'])
            ->paginate($perPage);
    }

    public function statics()
    {
        $statusActive = SubscriptionStatusEnum::ACTIVE->value;
        $statusPending = SubscriptionStatusEnum::PENDING->value;
        $billingCycleMonthly = SubscriptionBillingCycleEnum::MONTHLY->value;

        return Subscription::select([
            // Revenue: only from active, non-trialing subscriptions
            DB::raw("SUM(CASE
            WHEN status = '{$statusActive}'
                 AND (trial_ends_at IS NULL OR trial_ends_at <= NOW())
            THEN amount ELSE 0 END) as total_revenue"),

            DB::raw("SUM(CASE
            WHEN status = '{$statusActive}'
                 AND (trial_ends_at IS NULL OR trial_ends_at <= NOW())
                 AND billing_cycle = '{$billingCycleMonthly}'
            THEN amount ELSE 0 END) as monthly_revenue"),

            // Count active (includes trialing)
            DB::raw("SUM(CASE WHEN status = '{$statusActive}' THEN 1 ELSE 0 END) as total_active_subscriptions"),

            // Count trialing (subset of active)
            DB::raw("SUM(CASE
            WHEN status = '{$statusActive}'
                 AND trial_ends_at > NOW()
            THEN 1 ELSE 0 END) as total_trialing_subscriptions"),

            DB::raw("SUM(CASE WHEN status = '{$statusPending}' THEN 1 ELSE 0 END) as total_pending_subscriptions"),
        ])->first();
    }
}
