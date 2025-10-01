<?php

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\Users\PermissionDDLResource;
use App\Http\Resources\Tenant\Users\PermissionResource;
use App\Services\Tenant\Users\PermissionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(private readonly PermissionService $permissionService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = array_filter($request->only('group_name'), function ($value) {
            return $value !== null && $value !== false && $value !== '';
        });

        try {
            if ($request->has('groupBy')) {
                $permissions = $this->permissionService->getGroupedPermissions();
                $data = $permissions;
            } else {
                if ($request->has('ddl')) {
                    $permissions = $this->permissionService->index(filters: $filters);
                    $data = PermissionDDLResource::collection($permissions);
                } else {
                    $permissions = $this->permissionService->index(filters: $filters, perPage: per_page());
                    $data = PermissionResource::collection($permissions)->response()->getData(true);
                }
            }
            return apiResponse($data, 'Permissions retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
