<?php

namespace App\Services;

use App\Exceptions\GeneralException;
use App\Exceptions\NotFoundException;
use App\Models\Client;
use App\QueryFilters\ClientFilters;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\Client\ClientDTO;

class ClientService extends BaseService
{
    public function __construct(
        public Client $model,
        public LocationService $locationService,
    ) {}

    public function getModel(): Client
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $clients = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $clients->filter(new ClientFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = [])
    {
        $clients = $this->getQuery()->with($withRelations);
        return $clients->filter(new ClientFilters($filters));
    }

    public function getClients(array $filters = [], array $withRelations = [], ?int $perPage = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    /**
     * @throws NotFoundException
     * @throws GeneralException
     */
    public function store(ClientDTO $clientDTO)
    {
        //
        if (!$this->locationService->isArea($clientDTO->area_id)) {
            throw new GeneralException('Area not found');
        }
        // Create the client
        // Sync custom fields
        //        if ($clientDTO->customFields) {
        //            $customFieldsData = [];
        //            foreach ($clientDTO->customFields as $fieldId => $value) {
        //                $customFieldsData[$fieldId] = ['value' => $value];
        //            }
        //            $client->customFields()->sync($customFieldsData);
        //        }
        $clientData = $clientDTO->toArray();
        return $this->model->create($clientData)->load(['area', 'source']);
    }


    public function update(int $id, ClientDTO $clientDTO)
    {
        // Find the client by ID or fail if not found
        $client = $this->model->findOrFail($id);
        // Update client fields
        $clientData = $clientDTO->toArray();
        $client->update($clientData);
        // Handle industries relationship
        if (!empty($clientDTO->industries)) {
            $client->industries()->sync($clientDTO->industries);
        } else {
            $client->industries()->detach(); // Remove all industries if empty
        }
        // Handle services and categories
        $servicesData = [];
        if (!empty($clientDTO->services)) {
            foreach ($clientDTO->services as $serviceId) {
                if (is_numeric($serviceId) && $serviceId > 0) {
                    $categoryId = $clientDTO->serviceCategories[$serviceId] ?? null;
                    $servicesData[$serviceId] = ['category_id' => $categoryId];
                }
            }
            $client->services()->sync($servicesData);
        } else {
            $client->services()->detach();
        }
        // Handle custom fields relationship
        $customFieldsData = [];
        if (!empty($clientDTO->customFields)) {
            foreach ($clientDTO->customFields as $fieldId => $value) {
                $customFieldsData[$fieldId] = ['value' => $value];
            }
            $client->customFields()->sync($customFieldsData);
        } else {
            $client->customFields()->detach();
        }
        return $client;
    }


    public function delete(int $id)
    {
        return $this->getQuery()->where('id', $id)->delete();
    }
}
