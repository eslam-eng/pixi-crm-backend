<?php

namespace App\Http\Controllers\Central\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DepartmentRequest;
use App\Services\Central\DepartmentService;
use Exception;

class DepartmentController extends Controller
{
    public function __construct(private readonly DepartmentService $departmentService)
    {
    }

    public function index()
    {
        try {
            $departments = $this->departmentService->index();
            return ApiResponse::success(data: $departments, message: 'Departments retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 500);
        }
    }

    public function store(DepartmentRequest $request)
    {
        try {
            $department = $this->departmentService->store($request->validated());
            return ApiResponse::success(data: $department, message: 'Department created successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $id)
    {
        try {
            $department = $this->departmentService->findById($id);
            return ApiResponse::success(data: $department, message: 'Department retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 404);
        }
    }

    public function update(DepartmentRequest $request, int $id)
    {
        try {
            $department = $this->departmentService->update($id, $request->validated());
            return ApiResponse::success(data: $department, message: 'Department updated successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->departmentService->destroy($id);
            return ApiResponse::success(message: 'Department deleted successfully');
        } catch (Exception $e) {
            return ApiResponse::error(message: $e->getMessage(), code: 500);
        }
    }
}
