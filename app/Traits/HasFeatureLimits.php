<?php

namespace App\Traits;

use App\Services\Landlord\Actions\Subscription\FeatureUsageService;

trait HasFeatureLimits
{
    public function consumeFeature(string $slug, int $amount = 1): bool
    {
        return app(FeatureUsageService::class)->consumeIfAvailable($this->id, $slug, $amount);
    }

    public function releaseFeatureUsage(string $slug, int $amount = 1): void
    {
        app(FeatureUsageService::class)->releaseFeatureUsage($this->id, $slug, $amount);
    }
}
