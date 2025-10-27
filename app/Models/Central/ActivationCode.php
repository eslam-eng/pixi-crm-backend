<?php

namespace App\Models\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ActivationCode extends Model
{
    use HasUuids, Filterable, HasFactory;

    protected $fillable = [
        'code', 'source_id',
        'validity_days', 'status',
        'plan_id', 'expired_at',
        'tenant_id', 'user_id', 'redeemed_at', 'collected_at',
    ];

    protected $casts = [
        // 'status' => ActivationCodeStatusEnum::class,
        'redeemed_at' => 'datetime',
        'collected_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expired_at && Carbon::parse($this->expired_at)->isPast();
    }

    // /**
    //  * Check if code is already redeemed.
    //  */
    public function isRedeemed(): bool
    {
        return $this->status == ActivationCodeStatusEnum::USED->value || $this->redeemed_at;
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function sourcePayoutItems(): HasMany|ActivationCode
    {
        return $this->hasMany(SourcePayoutItem::class);
    }

    public function markAsRedeemed(User $user)
    {
        $this->update(['redeemed_at' => now(), 'user_id' => $user->id]);
    }
}
