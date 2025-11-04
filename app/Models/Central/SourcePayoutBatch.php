<?php

namespace App\Models\Central;

use App\Enums\Landlord\SourcePayoutCollectionEnum;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class SourcePayoutBatch extends Model
{
    use Filterable, HasFactory;

    protected $fillable = [
        'source_id',
        'plan_id',
        'total_amount',
        'period_start',
        'period_end',
        'collected_at',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function isCollected(): string
    {
        return ! is_null($this->collected_at) ? SourcePayoutCollectionEnum::COLLECTED->value : SourcePayoutCollectionEnum::PENDING->value;
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payoutItems()
    {
        return $this->hasMany(SourcePayoutItem::class);
    }

    // Collected items
    public function collectedItems()
    {
        return $this->payoutItems()->whereNotNull('collected_at');
    }

    // Non-collected items
    public function nonCollectedItems()
    {
        return $this->payoutItems()->whereNull('collected_at');
    }
}
