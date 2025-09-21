<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Services\Tenant\Users\RoleService;
use App\Http\Resources\Tenant\Users\RoleDDLResource;
use App\Http\Resources\Tenant\Users\RoleResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roleService) {}

    public function index(Request $request): JsonResponse
    {
        try {
            if ($request->has('ddl')) {
                $roles = $this->roleService->index();
                $data = RoleDDLResource::collection($roles);
            } else {
                $roles = $this->roleService->index(perPage: per_page());
                $data = RoleResource::collection($roles)->response()->getData(true);
            }
            return apiResponse($data, 'Roles retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
