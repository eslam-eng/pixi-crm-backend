<?php

namespace App\Models\Central;

use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasUuids, Filterable, HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'plan_id',
        'tenant_id',
        'monthly_credit_tokens',
        'status',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'is_trial',
        'auto_renew',
        'plan_snapshot',
        'cancelled_at',
        'amount',
        'currency',
        'billing_cycle',
    ];

    protected $casts = [
        'status' => SubscriptionStatusEnum::class,
        'billing_cycle' => SubscriptionBillingCycleEnum::class,
        'canceled_at' => 'datetime',
        'auto_renew' => 'boolean',
        'plan_snapshot' => 'array',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function featureSubscriptions(): Subscription|HasMany
    {
        return $this->hasMany(FeatureSubscription::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'feature_subscriptions')
            ->withPivot('value', 'usage', 'slug', 'name', 'group')
            ->using(FeatureSubscription::class);
    }

    protected function planName(): Attribute
    {
        $locale = app()->getLocale();

        return Attribute::make(
            get: fn() => Arr::get($this->plan_snapshot, 'name.' . $locale, $this->plan_snapshot['name']['en']) ?? null
        );
    }

    protected function startsAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => isset($this->starts_at) ? Carbon::parse($this->starts_at)->format('Y-m-d H:i') : null
        );
    }

    protected function endsAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => isset($this->ends_at) ? Carbon::parse($this->ends_at)->format('Y-m-d H:i') : null
        );
    }

    protected function trialEndsAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => isset($this->trial_ends_at) ? Carbon::parse($this->trial_ends_at)->format('Y-m-d H:i') : null
        );
    }

    protected function daysLeft(): Attribute
    {
        $today = Carbon::today();

        return Attribute::make(
            get: fn() => $this->ends_at->diffInDays($today)
        );
    }

    // Check if subscription is in trial period
    public function isOnTrial(): bool
    {
        return
            $this->trial_ends_at !== null &&
            $this->trial_ends_at->isFuture();
    }

    // Check if trial has expired
    public function isTrialExpired(): bool
    {
        return
            $this->trial_ends_at !== null &&
            $this->trial_ends_at->isPast();
    }

    // Get days remaining in trial
    public function getTrialDaysRemaining(): int
    {
        if (! $this->isOnTrial()) {
            return 0;
        }

        return max(0, $this->trial_ends_at->diffInDays(now()));
    }

    public function daysRemaining(): float|int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->ends_at);
    }

    public function isActive(): bool
    {
        return ! in_array($this->status, SubscriptionStatusEnum::inactive()) &&
            $this->starts_at <= now() &&
            ($this->ends_at == null || $this->ends_at > now());
    }

    public function getFeatureBySlug(string $slug): ?BaseLandlordModel
    {
        return $this->featureSubscriptions()->where('slug', $slug)->first();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Scope a query to only include active subscriptions for a given tenant.
     */
    public function scopeActive(Builder $query, int $tenantId): Builder
    {
        return $query
            ->whereNotIn('status', SubscriptionStatusEnum::inactive())
            ->whereDate('starts_at', '<=', now())
            ->whereDate('ends_at', '>=', now());
    }

    public static function generateSubscriptionNumber(): string
    {
        $prefix = config('subscription.prefix', 'SUB');
        $year = date('Y');
        $month = date('m');

        // Get the max sequence for the current month
        $lastSequence = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('MAX(CAST(RIGHT(subscription_number, 4) AS UNSIGNED)) as max_seq')
            ->value('max_seq');

        $sequence = $lastSequence ? $lastSequence + 1 : 1;

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (empty($subscription->subscription_number)) {
                $subscription->subscription_number = static::generateSubscriptionNumber();
            }
        });
    }
}
