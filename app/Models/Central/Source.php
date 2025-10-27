<?php

namespace App\Models\Central;

use App\Enum\ActivationStatusEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use Filterable, HasFactory;

    protected $fillable = [
        'name',
        'payout_percentage',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'payout_percentage' => 'decimal:2',
    ];

    /**
     * Get the activation codes for the source.
     */
    // public function activationCodes(): HasMany
    // {
    //     return $this->hasMany(ActivationCode::class);
    // }

    public function payoutBatches(): HasMany|Source
    {
        return $this->hasMany(SourcePayoutBatch::class);
    }

    // public function payoutItems()
    // {
    //     return $this->hasMany(SourcePayoutItem::class);
    // }
}
