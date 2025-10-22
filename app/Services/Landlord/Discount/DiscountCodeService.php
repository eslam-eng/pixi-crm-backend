<?php

namespace App\Services\Landlord\Discount;

use App\DTOs\Landlord\DiscountCodeDTO;
use App\Exceptions\DiscountCodeException;
use App\Models\Landlord\DiscountCode;
use App\Models\Landlord\Tenant;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class DiscountCodeService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return null; // You can implement filtering later if needed
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
        return $this->getQuery()->create($dto->toArray());
    }

    public function update(DiscountCode|int $discount, DiscountCodeDTO $dto): bool
    {
        if (is_int($discount)) {
            $discount = $this->findById($discount);
        }

        return $discount->update($dto->toArray());
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

        if (! $discountCode) {
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

        return $discountCode;
    }
}
