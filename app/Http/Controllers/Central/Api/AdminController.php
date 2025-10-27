<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\AdminDTO;
use App\Enums\Landlord\ActivationStatusEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\ChangeLocalRequest;
use App\Http\Requests\Central\AdminRequest;
use App\Http\Resources\Central\AdminResource;
use App\Http\Resources\Central\AuthUserResource;
use App\Models\Admin;
use App\Services\Central\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(protected readonly AdminService $adminService) {}

    public function index(Request $request)
    {
        $filters = $request->all();
        $limit = $request->input('limit', 15);
        $admins = $this->adminService->paginate(filters: $filters, perPage: $limit);

        return AdminResource::collection($admins);
    }

    public function show(Admin|int $admin)
    {
        $admin_id = $admin instanceof Admin ? $admin->id : $admin;
        $admin = $this->adminService->findById(id: $admin_id);

        return AdminResource::make($admin);
    }

    public function store(AdminRequest $request)
    {
        $adminDTO = AdminDTO::fromRequest($request);
        $this->adminService->create($adminDTO);

        return ApiResponse::success(message: 'Admin created successfully');
    }

    public function update(AdminRequest $request, Admin|int $admin)
    {
        $admin_id = $admin instanceof Admin ? $admin->id : $admin;
        $adminDTO = AdminDTO::fromRequest($request);
        $this->adminService->update($admin_id, $adminDTO);

        return ApiResponse::success(message: 'Admin updated successfully');
    }

    public function destroy(Admin|int $admin)
    {
        $admin_id = $admin instanceof Admin ? $admin->id : $admin;
        $this->adminService->delete($admin_id);

        return ApiResponse::success(message: 'Admin deleted successfully');
    }

    public function profile()
    {
        $user = auth()->guard('landlord')->user();

        return ApiResponse::success(data: AuthUserResource::make($user));
    }

    public function updateLocale(ChangeLocalRequest $request)
    {
        $user = auth()->guard('landlord')->user();
        $user->locale = $request->input('locale');
        $user->save();

        return ApiResponse::success(message: 'Locale updated successfully.');
    }

    public function toggleStatus(Admin|int $admin)
    {
        if (is_int($admin)) {
            $admin = $this->adminService->findById(id: $admin);
        }
        $admin->is_active = $admin->is_active === ActivationStatusEnum::ACTIVE
            ? ActivationStatusEnum::INACTIVE
            : ActivationStatusEnum::ACTIVE;
        $admin->save();

        return ApiResponse::success(message: 'Status updated successfully.');
    }
}
