<?php

namespace App\Services\Landlord;

use App\DTOs\Landlord\SourceDTO;
use App\Exceptions\CannotDeleteResourceException;
use App\Models\Landlord\Source;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class SourceService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return null;
    }

    protected function baseQuery(): Builder
    {
        return Source::query();
    }

    /**
     * Create a new source
     */
    public function create(SourceDTO $dto): Source
    {
        return $this->baseQuery()
            ->create($dto->toArray());
    }

    /**
     * Update an existing source
     */
    public function update(int $id, SourceDTO $dto): Model
    {
        $source = $this->findById($id);
        $source->update($dto->toArray());

        return $source;
    }

    /**
     * Delete a source
     *
     * @throws CannotDeleteResourceException
     */
    public function delete(int $id): bool
    {
        $source = $this->findById($id);

        if (! $source->delete()) {
            throw new CannotDeleteResourceException(
                'Cannot delete source because it has related records.'
            );
        }

        return true;
    }

    /**
     * Get paginated list of sources
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->getQuery($filters)->paginate();
    }

    /**
     * Get all sources
     */
    public function list(array $filters = []): Collection
    {
        return $this
            ->getQuery($filters)
            ->withSum(['payoutBatches as payout_batch_amount' => fn ($query) => $query->whereNull('collected_at')], 'total_amount')
            ->get();
    }

    /**
     * Toggle source active status
     */
    public function toggleStatus(int $id): bool
    {
        $source = $this->findById($id);
        $source->is_active = ! $source->is_active;

        return $source->save();
    }
}
