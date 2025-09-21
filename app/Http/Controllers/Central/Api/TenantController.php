<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\TenantCollection;
use App\Models\Tenant;
use Exception;

class TenantController extends Controller
{
    public function index()
    {
        try {
            $tenant = Tenant::with('user')->get();
            return ApiResponse(new TenantCollection($tenant), 'Tenants retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
