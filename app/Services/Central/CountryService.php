<?php

namespace App\Services\Central;

use App\Models\Country;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;

class CountryService extends BaseService
{
    public function __construct(private Country $model)
    {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getAll(?int $perPage = null)
    {
        $query = $this->getQuery()->orderBy('id', 'desc');
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
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
