<?php

namespace App\Services\Landlord\Actions\Subscription;

use App\Exceptions\SubscriptionException;
use App\Models\Landlord\Subscription;

class FeatureUsageService
{
    /**
     * Attempt to increment feature usage across multiple subscriptions.
     *
     * @throws \Throwable
     */
    public function consumeFeature($tenant, $featureSlug, $amount = 1)
    {
        $subscription = Subscription::query()
            ->where('tenant_id', $tenant->id)
            ->active()
            ->with('featureSubscriptions')
            ->first();

        if (! $subscription) {
            return false;
        }

        $feature = $subscription->featureSubscriptions->firstWhere('slug', $featureSlug);

        if (! $feature || ($feature->limit - $feature->used) < $amount) {
            throw new SubscriptionException("You have reached the limit for {$feature?->name}. Please upgrade your subscription.");
        }

        return $feature->increment('used', $amount);
    }

    /**
     * Check if feature is available for tenant
     */
    public function canUseFeature($tenant, string $featureSlug, int $amount = 1): bool
    {
        $subscription = Subscription::where('tenant_id', $tenant->id)
            ->active()
            ->with('featureSubscriptions')
            ->first();

        if (! $subscription) {
            throw new SubscriptionException('Subscription not found');
        }

        $feature = $subscription->featureSubscriptions->firstWhere('slug', $featureSlug);

        return $feature && $feature->remaining() >= $amount;
    }

    /**
     * Release feature usage (decrement).
     *
     * @throws SubscriptionException
     */
    public function releaseFeature($tenant, string $featureSlug, int $amount = 1): bool
    {
        $subscription = Subscription::query()
            ->where('tenant_id', $tenant->id)
            ->active()
            ->with('featureSubscriptions')
            ->first();

        if (! $subscription) {
            throw new SubscriptionException('No active subscription found.');
        }

        $feature = $subscription->featureSubscriptions->firstWhere('slug', $featureSlug);

        if (! $feature) {
            throw new SubscriptionException("Feature {$featureSlug} not found in active subscription.");
        }

        // Ensure we don’t go below 0
        if ($feature->used < $amount) {
            return $feature->update(['used' => 0]);
        }

        return $feature->decrement('used', $amount);
    }
}
