<?php

namespace App\Services\Central\ActivationCode;

use App\DTO\Central\SourcePayoutBatchDTO;
use App\Models\Central\ActivationCode;
use App\Models\Central\Filters\PayoutSourceFilters;
use App\Models\Central\Plan;
use App\Models\Central\Source;
use App\Models\Central\SourcePayoutBatch;
use App\Models\Central\SourcePayoutItem;
use App\Services\Central\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * service that handle source activation code collection for reedemed activation codes
 */
class SourcePayoutBatchService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return PayoutSourceFilters::class;
    }

    protected function baseQuery(): Builder
    {
        return SourcePayoutBatch::query();
    }

    public function paginate(?array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getQuery(filters: $filters)
            ->with(['plan:id,name', 'source'])
            ->withCount([
                'payoutItems',
                'collectedItems',
                'nonCollectedItems',
            ])
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Generate codes based on DTO and insert in one query
     */
    public function create(SourcePayoutBatchDTO $sourcePayoutBatchDTO)
    {
        return DB::connection('landlord')->transaction(function () use ($sourcePayoutBatchDTO) {
            $source = Source::query()->find($sourcePayoutBatchDTO->source_id);
            $plan = Plan::query()->find($sourcePayoutBatchDTO->plan_id);
            if (! $source) {
                throw new NotFoundHttpException(message: 'Selected source not found');
            }

            if (! $plan) {
                throw new NotFoundHttpException(message: 'Plan not found');
            }

            // Redeemed codes for this source + plan + period
            $codes = ActivationCode::query()
                ->where('source_id', $sourcePayoutBatchDTO->source_id)
                ->where('plan_id', $sourcePayoutBatchDTO->plan_id)
                ->whereBetween(DB::raw('DATE(redeemed_at)'), [$sourcePayoutBatchDTO->period_start, $sourcePayoutBatchDTO->period_end])
                ->whereDoesntHave('sourcePayoutItems')
                ->get(['id']);
            // check that there is codes for this selected periond
            if ($codes->isEmpty()) {
                throw new NotFoundHttpException(message: 'No codes found for this period');
            }

            // Create new payout
            $payout = $this->getQuery()->create($sourcePayoutBatchDTO->toArray());

            $percentage = $source->payout_percentage / 100;

            if ($codes->isNotEmpty()) {
                $now = now();
                $items = $codes->map(fn($code) => [
                    'source_payout_batch_id' => $payout->id,
                    'activation_code_id' => $code->id,
                    'payout_amount' => $plan->lifetime_price * $percentage,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->toArray();

                SourcePayoutItem::query()->insert($items);
            }

            $amount = $codes->count() * $plan->lifetime_price * $percentage;

            $payout->update(['total_amount' => $amount]);

            return $payout;
        });
    }

    public function markCollected($source_payout_batch_id): bool
    {
        $sourcePayoutBatch = $this->findById($source_payout_batch_id);
        $now = now();
        // update the collected_at field in payout and payout_items
        SourcePayoutItem::query()->where('source_payout_batch_id', $source_payout_batch_id)->update(['collected_at' => $now]);

        return $sourcePayoutBatch->update(['collected_at' => $now]);
    }

    public function collectSpaceficCodes($source_payout_batch_id, ?array $payout_item_ids): bool
    {
        // update the collected_at field in payout and payout_items
        $updated = SourcePayoutItem::query()
            ->where('source_payout_batch_id', $source_payout_batch_id)
            ->whereIn('id', $payout_item_ids)
            ->whereNull('collected_at')
            ->update(['collected_at' => now()]);

        // Step 2: Check if all items are collected
        $remaining = SourcePayoutItem::query()
            ->where('source_payout_batch_id', $source_payout_batch_id)
            ->whereNull('collected_at')
            ->exists();
        if (! $remaining) {
            // Step 3: Mark the payout as collected
            // Step 3: Mark payout as collected
            SourcePayoutBatch::query()
                ->where('id', $source_payout_batch_id)
                ->update(['collected_at' => now()]);
        }

        return $updated;
    }

    public function getPayoutItems($source_payout_batch_id)
    {
        return $this->getQuery()
            ->where('id', $source_payout_batch_id)
            ->withCount([
                'payoutItems as total_codes',
                'collectedItems as collected_codes',
                'nonCollectedItems as non_collected_codes',
            ])
            ->with(['payoutItems.activationCode.user' => fn($query) => $query->select('id', 'name', 'email')])
            ->first();
    }

    /**
     * Generate a single code with parts
     */
    protected function generateSingleCode(int $parts = 2, int $partLength = 3): string
    {
        return collect(range(1, $parts))
            ->map(fn() => Str::upper(Str::random($partLength)))
            ->implode('-');
    }

    public function delete(ActivationCode|int $activationCode): ?bool
    {
        if (is_int($activationCode)) {
            $activationCode = $this->findById($activationCode);
        }

        return $activationCode->delete();
    }
}
