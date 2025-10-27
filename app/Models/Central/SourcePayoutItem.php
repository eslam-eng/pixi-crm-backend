<?php

namespace App\Models\Central;

use App\Enums\Landlord\SourcePayoutCollectionEnum;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SourcePayoutItem extends Model
{
    use Filterable, HasFactory;

    protected $fillable = ['source_payout_batch_id', 'activation_code_id', 'payout_amount', 'collected_at'];

    public function payoutBatch(): BelongsTo
    {
        return $this->belongsTo(SourcePayoutBatch::class);
    }

    public function activationCode(): BelongsTo
    {
        return $this->belongsTo(ActivationCode::class);
    }

    public function isCollected(): string
    {
        return ! is_null($this->collected_at) ? SourcePayoutCollectionEnum::COLLECTED->value : SourcePayoutCollectionEnum::PENDING->value;
    }
}
