<?php

namespace App\Http\Controllers\Api\Users;

use App\DTO\Tenant\Role\RoleDTO;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Roles\RoleRequest;
use App\Services\Tenant\Users\RoleService;
use App\Http\Resources\Tenant\Users\RoleDDLResource;
use App\Http\Resources\Tenant\Users\RoleResource;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roleService)
    {
        $this->middleware('permission:manage-settings')->except(['index', 'show']);
    }

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

    public function store(RoleRequest $request)
    {
        try {
            DB::beginTransaction();
            $dto = RoleDTO::fromRequest($request);
            $role = $this->roleService->create($dto);
            DB::commit();
            return apiResponse(new RoleResource($role), 'Role created successfully', code: Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $role_id)
    {
        try {
            $role = $this->roleService->findById($role_id);
            return apiResponse(new RoleResource($role), 'Role retrieved successfully');
        } catch (NotFoundException $e) {
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(RoleRequest $request, int $role_id)
    {
        try {
            DB::beginTransaction();
            $dto = RoleDTO::fromRequest($request);
            $role = $this->roleService->update($role_id, $dto);
            DB::commit();
            return apiResponse(new RoleResource($role), 'Role updated successfully');
        } catch (NotFoundException $e) {
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(int $role_id)
    {
        try {
            $this->roleService->destroy($role_id);
            return apiResponse(message: 'Role deleted successfully');
        } catch (NotFoundException $e) {
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
