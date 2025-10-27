<?php

namespace App\Services\Central;

use App\Models\Admin;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminService extends BaseService
{
    public function __construct(private Admin $model) {}

    public function getModel(): Model
    {
        return $this->model;
    }
}
