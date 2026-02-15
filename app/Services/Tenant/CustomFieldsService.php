<?php

namespace App\Services\Tenant;

use App\DTO\CustomField\CustomFieldDTO;
use App\Models\Tenant\CustomField;
use App\QueryFilters\CustomFieldFilters;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;


class CustomFieldsService extends BaseService
{
    public function __construct(
        public CustomField $model,
    ) {
    }

    public function getModel(): CustomField
    {
        return $this->model;
    }

    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $query = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $query->filter(new CustomFieldFilters($filters));
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function store(CustomFieldDTO $customFieldDTO): CustomField
    {
        return $this->model->create($customFieldDTO->toArray());
    }

    public function update(CustomField $customField, CustomFieldDTO $customFieldDTO): CustomField
    {
        $customField->update($customFieldDTO->toArray());
        return $customField;
    }

    public function show(int $id, array $withRelations = [])
    {
        return $this->getQuery()->with($withRelations)->findOrFail($id);
    }

    public function delete(int $id)
    {
        $customField = $this->getQuery()->findOrFail($id);
        return $customField->delete();
    }
}
