<?php

namespace App\Models\Central;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\DiscountCodeStatusEnum;
use Carbon\Carbon;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCode extends Model
{
    use Filterable, HasFactory;

    protected $fillable = [
        'discount_code',
        'plan_id',
        //        'discount_type',
        'discount_percentage',
        //        'users_limit',
        'usage_limit',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'status' => ActivationStatusEnum::class,
        'discount_percentage' => 'float',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isExpired(): bool
    {
        return Carbon::parse($this->expires_at)->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === ActivationStatusEnum::ACTIVE && ! $this->isExpired();
    }

    public function usages(): HasMany
    {
        return $this->hasMany(DiscountCodeUsage::class);
    }

    public function markAsUsed()
    {
        $this->update(['status' => DiscountCodeStatusEnum::USED->value]);
    }
}
