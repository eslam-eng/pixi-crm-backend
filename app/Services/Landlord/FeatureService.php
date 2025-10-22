<?php

namespace App\Services\Landlord;

use App\DTOs\Tenant\FeatureDTO;
use App\Models\Landlord\Feature;
use App\Models\Landlord\Filters\FeaturesFilter;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class FeatureService extends BaseService
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getQuery(filters: $filters)->paginate($perPage);
    }

    public function getFeatures(?array $filters = []): Collection
    {
        return $this->getQuery(filters: $filters)->get();
    }

    public function create(FeatureDTO $dto): Feature
    {
        return $this->getQuery()->create($dto->toArray());
    }

    public function update(int $id, FeatureDTO $dto): Model
    {
        $feature = $this->findById($id);
        $feature->update($dto->toArray());

        return $feature;
    }

    public function delete(int $id): bool
    {
        $feature = $this->findById($id);
        if ($feature) {
            return $feature->delete();
        }

        return false;
    }

    protected function getFilterClass(): ?string
    {
        return FeaturesFilter::class;
    }

    protected function baseQuery(): Builder
    {
        return Feature::query();
    }
}
