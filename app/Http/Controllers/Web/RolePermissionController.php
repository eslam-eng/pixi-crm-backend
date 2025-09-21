<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\DataTables\RolesDataTable;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Services\RolePermissionService;
use App\DTO\RolePermission\RolePermissionDTO;
use App\Http\Requests\Roles\RoleStoreRequest;

class RolePermissionController extends Controller
{
    public function __construct(protected RolePermissionService $rolePermissionService) {
        $this->middleware('permission:view role-permissions', ['only' => ['index']]);
        $this->middleware('permission:edit role-permissions', ['only' => ['edit','update']]);
        $this->middleware('permission:create role-permissions', ['only' => ['store','create']]);

    }

    // Show the role selection page
    public function index(RolesDataTable $dataTable,Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = [];
        return $dataTable->with(['filters' => $filters, 'withRelations' => $withRelations])->render('layouts.dashboard.rolePermission.index');
    }

    // Show the permissions for the selected role
    public function edit(Role $role)
    {
        $data = $this->rolePermissionService->getRolePermissions($role);
        return view('layouts.dashboard.rolePermission.edit', $data);
    }

    // Update the role's permissions
    public function update(Request $request, Role $role)
    {
        $dto = RolePermissionDTO::fromRequest($request);
        $this->rolePermissionService->updateRolePermissions($role, $dto);

        return redirect()->route('role-permissions.index', $role->id)
            ->with('toast', [
                'type' => 'success',
                'title' => 'Success',
                'message' => 'Role permissions updated successfully.',
            ]);
    }

    // Show role creation form
    public function create()
    {
        $permissions = $this->rolePermissionService->getAllPermissions();
        return view('layouts.dashboard.rolePermission.create', compact('permissions'));
    }

    // Store a new role and assign permissions
    public function store(RoleStoreRequest $request)
    {

        $this->rolePermissionService->createRoleWithPermissions($request->all());
        $toast = [
            'type' => 'success',
            'title' => 'success',
            'message' => trans('app.role_created_successfully')
        ];
        return to_route('role-permissions.index')->with('toast', $toast);
    }

    public function destroy(int $id)
    {
        try{
            $this->rolePermissionService->delete($id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.role_deleted_successfully')
            ];
            return to_route('role-permissions.index')->with('toast', $toast);
        }catch (\Exception $e) {
            $toast = [
                'type' => 'error',
                'title' => 'error',
                'message' => trans('app.there_is_an_error')
            ];
            return back()->with('toast', $toast);
        }
    }
}
