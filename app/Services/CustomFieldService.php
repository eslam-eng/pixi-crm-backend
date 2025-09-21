<?php

namespace App\Services;

use App\DTO\CustomField\CustomFieldDTO;
use App\Models\CustomField;
use App\QueryFilters\CustomFieldFilters;
use Illuminate\Database\Eloquent\Builder;


class CustomFieldService extends BaseService
{
    public function __construct(
        public CustomField               $model,
    ) {}

    public function getModel(): CustomField
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
        $custom_fields = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $custom_fields->filter(new CustomFieldFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = [])
    {
        $custom_fields = $this->getQuery()->with($withRelations);
        return $custom_fields->filter(new CustomFieldFilters($filters));
    }

    public function store(CustomFieldDTO $customFieldDTO): CustomField
    {
        return $this->model->create($customFieldDTO->toArray());
    }

    public function update(CustomFieldDTO $customFieldDTO, $id){
        $customField=$this->findById($id);
        $customField->update($customFieldDTO->toArray());
        return true;
    }

    public function delete(int $id)
    {
        return $this->getQuery()->where('id', $id)->delete();
    }

}
