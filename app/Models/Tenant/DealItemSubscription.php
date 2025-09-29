<?php

namespace App\Models\Tenant;

use App\Enums\BillingCycleEnum;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DealItemSubscription extends Model
{
    use Filterable;

    protected $fillable = [
        'deal_item_id',
        'start_at',
        'end_at',
        'billing_cycle',
    ];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
        'billing_cycle' => BillingCycleEnum::class,
    ];

    /**
     * Get the deal item that owns the subscription.
     */
    public function dealItem(): BelongsTo
    {
        return $this->belongsTo(DealItem::class);
    }

    /**
     * Get the deal through the deal item.
     */
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'deal_id', 'id')
            ->through('dealItem');
    }

    /**
     * Check if the subscription is currently active.
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $now->between($this->start_at, $this->end_at);
    }

    /**
     * Check if the subscription has expired.
     */
    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->end_at);
    }

    /**
     * Get the duration in days.
     */
    public function getDurationInDays(): int
    {
        return $this->start_at->diffInDays($this->end_at);
    }

    /**
     * Get the next billing date.
     */
    public function getNextBillingDate(): Carbon
    {
        $days = $this->billing_cycle->getDays();
        return $this->start_at->addDays($days);
    }

    /**
     * Scope to get active subscriptions.
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('start_at', '<=', $now)
                    ->where('end_at', '>=', $now);
    }

    /**
     * Scope to get expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where('end_at', '<', Carbon::now());
    }

    /**
     * Scope to get subscriptions by billing cycle.
     */
    public function scopeByBillingCycle($query, BillingCycleEnum $cycle)
    {
        return $query->where('billing_cycle', $cycle->value);
    }
}