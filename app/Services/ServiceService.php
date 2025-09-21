<?php

namespace App\Services;

use App\Models\Service;
use App\DTO\Service\ServiceDTO;
use Illuminate\Support\Facades\Log;
use App\QueryFilters\ServiceFilters;
use Illuminate\Database\Eloquent\Builder;

class ServiceService extends BaseService{
    public $user;

    public function __construct(
        public Service               $model,
    ) {}

    public function getModel(): Service
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function getTableName(): String
    {
        return $this->getModel()->getTable();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $services = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $services->filter(new ServiceFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = [])
    {
        $services = $this->getQuery()->with($withRelations);
        return $services->filter(new ServiceFilters($filters));
    }

    public function store(ServiceDTO $serviceDTO): Service
    {
        $service = Service::create([
            'name' => $serviceDTO->getName(),
            'price' => $serviceDTO->getPrice(), // Direct price (nullable)
        ]);

        // Create categories for the service (if any)
        if (!empty($serviceDTO->getCategories())) {
            foreach ($serviceDTO->getCategories() as $categoryData) {
                if (!empty($categoryData['name']) && !empty($categoryData['price'])) {
                    $service->categories()->create([
                        'name' => $categoryData['name'],
                        'price' => $categoryData['price'],
                    ]);
                }
            }
        }

        return $service;
    }

    public function update(ServiceDTO $serviceDTO, $id): Service
    {
        $service = $this->findById($id);
        $service->update([
            'name' => $serviceDTO->getName(),
            'price' => $serviceDTO->getPrice(), // Direct price (nullable)
        ]);

        // Sync categories for the service (if any)
        if (!empty($serviceDTO->getCategories())) {
            $service->categories()->delete(); // Delete existing categories
            foreach ($serviceDTO->getCategories() as $categoryData) {
                if (!empty($categoryData['name']) && !empty($categoryData['price'])) {
                    $service->categories()->create([
                        'name' => $categoryData['name'],
                        'price' => $categoryData['price'],
                    ]);
                }
            }
        }

        return $service;
    }

    public function delete(int $id)
    {
        return $this->getQuery()->where('id', $id)->delete();
    }


}
