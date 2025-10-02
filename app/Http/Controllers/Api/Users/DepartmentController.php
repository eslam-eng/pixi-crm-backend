<?php

namespace App\Http\Controllers\Api\Users;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Services\Tenant\Users\DepartmentService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Users\DepartmentDDLResource;
use App\Http\Resources\Tenant\Users\DepartmentResource;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(private readonly DepartmentService $departmentService) {}


    public function index(Request $request): JsonResponse
    {
        try {
            if ($request->has('ddl')) {
                $filters['is_active'] = 1;
                $departments = $this->departmentService->index(filters: $filters);
                $data = DepartmentDDLResource::collection($departments);
            } else {
                $departments = $this->departmentService->index( perPage: per_page());
                $data = DepartmentResource::collection($departments)->response()->getData(true);
            }
            return ApiResponse($data, 'Departments retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
