<?php

namespace App\Services;
use App\Models\Resource;
use App\DTO\Resource\ResourceDTO;
use App\Models\Source;
use App\QueryFilters\ResourceFilters;
use Illuminate\Database\Eloquent\Builder;

class ResourceService extends BaseService
{
    public function __construct(
        public Source $model,
    ) {}

    public function getModel(): Source
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
        $resources = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $resources->filter(new ResourceFilters($filters));
    }


    public function datatable(array $filters = [], array $withRelations = [])
    {
        $resources = $this->getQuery()->with($withRelations);
        return $resources->filter(new ResourceFilters($filters));
    }

    public function getResources(array $filters = [], array $withRelations = [], ?int $perPage = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        return $perPage ? $query->paginate($perPage): $query->get();
    }

    public function store(ResourceDTO $resourceDTO){
        $resource_data=$resourceDTO->toArray();
        $resource= $this->model->create($resource_data);
        if ($resourceDTO->getImage()) {
            $resource->addMedia($resourceDTO->getImage())->toMediaCollection('sources');
        }
        return $resource;
    }

    public function update(ResourceDTO $resourceDTO,$id){
        $resource=$this->findById($id);
        $resource->update($resourceDTO->toArray());
        return true;
    }

    public function delete(int $id)
    {
        return $this->getQuery()->where('id', $id)->delete();
    }
}
