<?php

namespace App\Services\Central\Subscription;

use App\DTO\Central\SubscriptionDTO;
use App\Exceptions\TrialException;
use App\Models\Central\Invoice;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;
use App\Services\Central\Invoice\InvoiceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CreateSubscriptionService
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    protected function getFilterClass(): ?string
    {
        return null;
    }

    protected function baseQuery(): Builder
    {
        return Subscription::query();
    }

    /**
     * @return invoice | null
     *
     * @throws \Throwable
     * @throws TrialException
     */
    public function handle(SubscriptionDTO $subscriptionDTO)
    {
        $plan = Plan::query()->find($subscriptionDTO->plan_id);
        $subscriptionData = $this->prepareSubscriptionData($subscriptionDTO, $plan);

        return DB::connection('landlord')->transaction(function () use ($subscriptionData, $subscriptionDTO, $plan) {
            $subscription = $this->baseQuery()->create($subscriptionData);
            $invoice = null;
            if ($subscriptionDTO->shouldCreateInvoice) {
                $invoiceDTO = $subscriptionDTO->invoiceDTO;
                $invoiceDTO->subscription_id = $subscription->id;
                $invoice = $this->invoiceService->create(invoiceDTO: $invoiceDTO);
            }

            $this->createFeatureSubscriptions($plan, $subscription);

            return $invoice;
        });
    }

    public function prepareSubscriptionData(SubscriptionDTO $subscriptionDTO, Plan $plan)
    {
        $subscriptionData = $subscriptionDTO->toArray();

        if (! empty($subscriptionPlanDTO->plan_snapshot)) {
            $subscriptionData['plan_snapshot'] = json_encode($subscriptionPlanDTO->plan_snapshot);
        } else {
            $planSnapshot = $plan->only($plan->getFillable());
            $planSnapshot['name'] = $plan->getTranslations('name');
            $subscriptionData['plan_snapshot'] = json_encode($planSnapshot);
        }

        return $subscriptionData;
    }

    protected function createFeatureSubscriptions(Plan $plan, Subscription $subscription): void
    {
        $featureSubscriptions = $plan->features->map(function ($feature) use ($subscription) {
            return [
                'subscription_id' => $subscription->id,
                'feature_id' => $feature->id,
                'slug' => $feature->slug,
                'name' => json_encode($feature->getTranslations('name')),
                'group' => $feature->group,
                'value' => $feature->pivot->value,
                'usage' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        if (! empty($featureSubscriptions)) {
            DB::connection('landlord')->table('feature_subscriptions')->insert($featureSubscriptions);
        }
    }
}
