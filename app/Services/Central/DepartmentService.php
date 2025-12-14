<?php

namespace App\Services\Central;

use App\Models\Central\Department;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;

class DepartmentService extends BaseService
{
    public function __construct(private Department $model)
    {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function index(?int $perPage = null)
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
        $department = $this->findById($id);
        $department->update($data);
        return $department;
    }

    public function destroy(int $id)
    {
        $department = $this->findById($id);
        return $department->delete();
    }
}
