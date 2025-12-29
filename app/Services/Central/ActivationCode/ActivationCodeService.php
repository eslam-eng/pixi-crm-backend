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
        return $this->getQuery(filters: $filters, withRelation: ['plan:id,name', 'source', 'user:id,first_name,last_name,email', 'createdBy:id,name'])

            ->paginate($perPage);
    }

    /**
     * Generate codes based on DTO and insert in one query
     */
    public function store(ActivationCodeDTO $dto): array
    {
        $validUntil = now()->addDays($dto->validityDays);
        // Prepare all codes in memory
        $codesData = collect(range(1, $dto->count))
            ->map(function () use ($dto, $validUntil) {
                return [
                    'code' => $dto->code ?? $this->generateSingleCode($dto->parts, $dto->partLength),
                    'plan_id' => $dto->planId,
                    'validity_days' => $dto->validityDays,
                    'source_id' => $dto->source_id,
                    'created_by_id' => $dto->created_by_id,
                    'expired_at' => $validUntil,
                    'status' => $dto->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->all();

        // Insert all codes in one query
        $this->getQuery()->insert($codesData);

        return $codesData;
    }

    public function generateCode()
    {
        return $this->generateSingleCode(3, 4);

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

    public function delete(ActivationCode|string|int $activationCode): ?bool
    {

        if (!$activationCode instanceof ActivationCode) {
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
            DB::raw("SUM(CASE WHEN status = '" . ActivationCodeStatusEnum::EXPIRED->value . "' AND id NOT IN (SELECT activation_code_id FROM source_payout_items WHERE collected_at IS NOT NULL) THEN 1 ELSE 0 END) as expired"),
            DB::raw('(SELECT COUNT(*) FROM source_payout_items WHERE collected_at IS NOT NULL) as collected'),
        ])->first();
    }

    public function exportCodesRequest(array $inputs)
    {
        return $this->baseQuery()
            ->whereIn('id', $inputs['ids'])
            ->get()
            ->map(function ($code) {
                // Ensure array shape matches what ActivationCodesExport expects if it relies on array structure
                // But ActivationCodesExport iterates valid models/arrays.
                return $code;
            })
            ->toArray();
    }

    public function updateStatus(array $inputs): bool
    {
        return $this->baseQuery()
            ->whereIn('id', $inputs['ids'])
            ->update(['status' => $inputs['status']]);
    }
    public function deleteMulti(array $ids): bool
    {
        return $this->baseQuery()->whereIn('id', $ids)->delete();
    }

    public function checkActivationCode($code)
    {
        return $this->baseQuery()
            ->where('code', $code)
            ->where('status', ActivationCodeStatusEnum::AVAILABLE->value)
            ->whereDate('expired_at', '>', now())
            ->first();
    }
}
