<?php

namespace App\Services\Central;

use App\DTO\Central\IndustryDTO;
use App\Exceptions\CannotDeleteResourceException;
use App\Models\Central\Industry;
use App\Services\Central\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class IndustryService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return null;
    }

    protected function baseQuery(): Builder
    {
        return Industry::query();
    }

    /**
     * Get all industries or paginated
     */
    public function index(?int $perPage = null)
    {
        $query = $this->getQuery()->orderBy('id', 'desc');
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    /**
     * Create a new industry
     */
    public function store(array $data): Industry
    {
        return $this->baseQuery()->create($data);
    }

    /**
     * Create a new industry using DTO
     */
    public function create(IndustryDTO $dto): Industry
    {
        return $this->baseQuery()
            ->create($dto->toArray());
    }

    /**
     * Update an existing industry
     */
    public function update(int $id, array $data): Model
    {
        $industry = $this->findById($id);
        $industry->update($data);

        return $industry;
    }

    /**
     * Delete an industry
     *
     * @throws CannotDeleteResourceException
     */
    public function delete(int $id): bool
    {
        $industry = $this->findById($id);

        if (!$industry->delete()) {
            throw new CannotDeleteResourceException(
                'Cannot delete industry because it has related records.'
            );
        }

        return true;
    }

    /**
     * Delete an industry (alias for delete)
     */
    public function destroy(int $id): bool
    {
        $industry = $this->findById($id);
        return $industry->delete();
    }

    /**
     * Get paginated list of industries
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->getQuery($filters)->paginate();
    }

    /**
     * Get all industries
     */
    public function list(array $filters = []): Collection
    {
        return $this
            ->getQuery($filters)
            ->get();
    }

    /**
     * Toggle industry active status
     */
    public function toggleStatus(int $id): bool
    {
        $industry = $this->findById($id);
        $industry->is_active = !$industry->is_active;

        return $industry->save();
    }
}
