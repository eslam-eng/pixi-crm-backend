<?php

namespace App\Services\Central\Discount;

use App\DTO\Central\DiscountCodeDTO;
use App\Enums\Landlord\DiscountUsageEnum;
use App\Exceptions\DiscountCodeException;
use App\Models\Central\DiscountCode;
use App\Models\Central\Tenant;
use App\QueryFilters\DiscountCodeFilters;
use App\Services\Central\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class DiscountCodeService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return DiscountCodeFilters::class;
    }

    protected function baseQuery(): Builder
    {
        return DiscountCode::query();
    }

    public function paginate(?array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getQuery(filters: $filters)
            ->with('plan:id,name')
            ->latest()
            ->paginate($perPage);
    }

    public function create(DiscountCodeDTO $dto): DiscountCode
    {
        $data = $dto->toArray();
        if ($data['discount_type'] === DiscountUsageEnum::SINGLE_USE->value) {
            $data['usage_limit'] = 1;
        }

        return $this->getQuery()->create($data);
    }

    public function update(DiscountCode|int $discount, DiscountCodeDTO $dto): bool
    {
        if (is_int($discount)) {
            $discount = $this->findById($discount);
        }

        $data = $dto->toArray();
        if ($data['discount_type'] === DiscountUsageEnum::SINGLE_USE->value) {
            $data['usage_limit'] = 1;
        }

        return $discount->update($data);
    }

    public function delete(DiscountCode|int $discount): ?bool
    {
        if (is_int($discount)) {
            $discount = $this->findById($discount);
        }

        return $discount->delete();
    }

    public function toggleStatus(DiscountCode $discount): bool
    {
        $discount->status = $discount->status->toggle();

        return $discount->save();
    }

    /**
     * @throws DiscountCodeException
     */
    public function validateDiscountForPlan(string $code, int $planId, Tenant $tenant): DiscountCode
    {
        $discountCode = $this->baseQuery()
            ->where('discount_code', $code)
            ->where('plan_id', $planId)
            ->first();

        if (!$discountCode) {
            throw new DiscountCodeException('Invalid discount code.');
        }
        // Check expiry
        if ($discountCode->expires_at && now()->greaterThan($discountCode->expires_at)) {
            throw new DiscountCodeException('Discount code expired.');
        }

        // Check global usage
        if ($discountCode->usage_limit && $discountCode->usages()->count() >= $discountCode->usage_limit) {
            throw new DiscountCodeException('Discount code has been fully used.');
        }

        // Check users limit (unique tenants)
        if ($discountCode->users_limit) {
            $uniqueTenantsCount = $discountCode->usages()->distinct('tenant_id')->count('tenant_id');
            if ($uniqueTenantsCount >= $discountCode->users_limit) {
                throw new DiscountCodeException('Discount code has reached its user limit.');
            }
        }

        return $discountCode;
    }
}
