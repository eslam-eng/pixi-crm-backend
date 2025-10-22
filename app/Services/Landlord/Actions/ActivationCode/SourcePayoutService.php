<?php

namespace App\Services\Landlord\Actions\ActivationCode;

use App\Enum\ActivationCodeStatusEnum;
use App\Exceptions\NoActivationCodesException;
use App\Models\Landlord\ActivationCode;
use App\Models\Landlord\Source;
use App\Models\Landlord\SourcePayoutBatch;
use App\Models\Landlord\SourcePayoutItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SourcePayoutService
{
    /**
     * Create a payout batch for a source for a specific period.
     *
     * @throws NoActivationCodesException
     */
    public static function createBatch(Source $source, Carbon $periodStart, Carbon $periodEnd)
    {
        $codes = ActivationCode::query()
            ->where('source_id', $source->id)
            ->where('status', ActivationCodeStatusEnum::USED->value)
            ->whereDoesntHave('payoutItems')
            ->whereBetween('redeemed_at', [$periodStart, $periodEnd])
            ->get();

        if ($codes->isEmpty()) {
            throw new NoActivationCodesException;
        }

        $totalAmount = $codes->sum(fn ($c) => $c->payout_rate);

        return DB::connection('landlord')
            ->transaction(function () use ($codes, $source, $totalAmount, $periodStart, $periodEnd) {

                $batch = SourcePayoutBatch::create([
                    'source_id' => $source->id,
                    'total_amount' => $totalAmount,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                ]);

                // Prepare data for bulk insert
                $itemsData = $codes->map(function ($code) use ($batch) {
                    return [
                        'source_payout_batch_id' => $batch->id,
                        'activation_code_id' => $code->id,
                        'payout_amount' => $code->payout_rate,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                SourcePayoutItem::insert($itemsData);

                return $batch;
            });
    }
}
