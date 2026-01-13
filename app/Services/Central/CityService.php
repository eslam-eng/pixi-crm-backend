<?php

namespace App\Services\Central;

use App\Models\City;
use App\QueryFilters\Central\CityFilters;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CityService extends BaseService
{
    public function __construct(private City $model)
    {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $data = $this->getQuery()->with($withRelations);
        return $data->filter(new CityFilters($filters));
    }
    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function store(array $data)
    {
        return $this->getModel()->create($data);
    }

    public function update(int $id, array $data)
    {
        $result = $this->findById($id);
        $result->update($data);
        return $result;
    }

    public function destroy(int $id)
    {
        $result = $this->findById($id);
        return $result->delete();
    }
}
