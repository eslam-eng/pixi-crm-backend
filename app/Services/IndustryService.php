<?php

namespace App\Services;

use App\DTO\Industry\IndustryDTO;
use App\Models\Industry;
use App\QueryFilters\IndustryFilters;
use Illuminate\Database\Eloquent\Builder;


class IndustryService extends BaseService
{
    public function __construct(
        public Industry               $model,
    ) {}

    public function getModel(): Industry
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
        $industries = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $industries->filter(new IndustryFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = [])
    {

        $industries = $this->getQuery()->with($withRelations);
        return $industries->filter(new IndustryFilters($filters));
    }

    public function store(IndustryDTO $industryDTO)
    {
        $industry_data = $industryDTO->toArray();
        $industry = $this->model->create($industry_data);
        return $industry;
    }

    public function update(IndustryDTO $industryDTO, $id)
    {
        $industry = $this->findById($id);
        $industry->update($industryDTO->toArray());
        return true;
    }

    public function deleteMultiple(array $ids)
    {
        return $this->getQuery()->whereIn('id', $ids)->delete();
    }

    public function delete(int $id)
    {
        return $this->getQuery()->where('id', $id)->delete();
    }
}
