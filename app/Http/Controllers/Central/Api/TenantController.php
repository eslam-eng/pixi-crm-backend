<?php

namespace App\Http\Controllers\Central\Api;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Central\TenantDetailsResource;
use App\Http\Resources\Central\TenantResource;
use App\Services\Central\Tenant\TenantService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(private readonly TenantService $tenantService) {
    }

    public function index(Request $request)
    {
        $filters = array_filter($request->all(), fn ($value) => filled($value));
        $tenants = $this->tenantService->paginate(filters: $filters);

        return TenantResource::collection($tenants);
    }

    public function statics()
    {
        $statics = $this->tenantService->statics();

        return ApiResponse::success(data: $statics);
    }

    public function show($tenant)
    {
        $tenant = $this->tenantService->details(tenant_id: $tenant);

        return TenantDetailsResource::make($tenant);
    }

    public function destroy($tenant)
    {
        $this->tenantService->delete($tenant);

        return ApiResponse::success(message: 'Tenant deleted successfully');
    }

    public function toggleStatus(string $tenant_id)
    {
        $tenant = $this->tenantService->findById(id: $tenant_id);

        // status act is is_active or not for now
        $tenant->status = $tenant->status == ActivationStatusEnum::ACTIVE
            ? ActivationStatusEnum::INACTIVE->value
            : ActivationStatusEnum::ACTIVE->value;

        $tenant->save();

        return ApiResponse::success(message: 'Status updated successfully.');
    }
}
