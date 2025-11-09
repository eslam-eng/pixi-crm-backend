<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FeatureSubscription extends Pivot
{
    protected $table = 'feature_subscriptions';

    protected $fillable = [
        'subscription_id', 'feature_id', 'value',
        'usage', 'slug', 'name', 'group',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(FeaturePlan::class, 'feature_id');
    }

    // Check if limit is exceeded
    public function isLimitExceeded($additionalUsage = 0): bool
    {
        if ($this->limit_value === -1) {
            return false; // Unlimited
        }

        return ($this->usage + $additionalUsage) > $this->limit_value;
    }

    // Get available quota
    public function getAvailableQuota()
    {
        if ($this->limit_value === -1) {
            return 'unlimited';
        }

        return max(0, $this->limit_value - $this->usage);
    }

    // Get usage percentage
    public function getUsagePercentage()
    {
        if ($this->limit_value === -1 || $this->limit_value == 0) {
            return 0;
        }

        return min(100, ($this->usage / $this->limit_value) * 100);
    }
}
