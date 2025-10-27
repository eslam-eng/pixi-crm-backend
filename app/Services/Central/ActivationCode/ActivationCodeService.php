<?php

namespace App\Services\Central\ActivationCode;

use App\DTO\Central\ActivationCodeDTO;
use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Models\Central\ActivationCode;
use App\Models\Central\Filters\ActivationCodeFilters;
use App\Services\Central\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivationCodeService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return ActivationCodeFilters::class;
    }

    protected function baseQuery(): Builder
    {
        return ActivationCode::query();
    }

    public function paginate(?array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getQuery(filters: $filters)
            ->with(['plan:id,name', 'source', 'user:id,name,email'])
            ->paginate($perPage);
    }

    /**
     * Generate codes based on DTO and insert in one query
     */
    public function generate(ActivationCodeDTO $dto): bool
    {
        $validUntil = now()->addDays($dto->validityDays);

        // Prepare all codes in memory
        $codesData = collect(range(1, $dto->count))
            ->map(function () use ($dto, $validUntil) {
                return [
                    'id' => Str::uuid(),
                    'code' => $this->generateSingleCode($dto->parts, $dto->partLength),
                    'plan_id' => $dto->planId,
                    'validity_days' => $dto->validityDays,
                    'source_id' => $dto->source_id,
                    'expired_at' => $validUntil,
                    'status' => $dto->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->all();

        // Insert all codes in one query
        return $this->getQuery()->insert($codesData);
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

    public function statics()
    {
        return $this->baseQuery()->select([
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN status = '" . ActivationCodeStatusEnum::AVAILABLE->value . "' THEN 1 ELSE 0 END) as active"),
            DB::raw("SUM(CASE WHEN status = '" . ActivationCodeStatusEnum::USED->value . "' AND id NOT IN (SELECT activation_code_id FROM source_payout_items WHERE collected_at IS NOT NULL) THEN 1 ELSE 0 END) as used"),
            DB::raw('(SELECT COUNT(*) FROM source_payout_items WHERE collected_at IS NOT NULL) as collected'),
        ])->first();
    }
}
