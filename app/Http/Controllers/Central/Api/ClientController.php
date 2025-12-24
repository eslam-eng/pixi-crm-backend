<?php

namespace App\Http\Controllers\Central\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\ClientRequest;
use App\Http\Resources\Tenant\TenantResource;
use App\Models\Tenant;
use App\Services\Central\ClientService;
use Exception;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(private readonly ClientService $clientServie)
    {
    }
    public function index()
    {
        try {
            $client = $this->clientServie->paginate();
            $data = TenantResource::collection($client)->response()->getData(true);
            return ApiResponse($data, 'Tenants retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(ClientRequest $request)
    {
        try {
            dd($request->validated());
            // Validation logic here

            // Create tenant logic here

            return ApiResponse(message: 'Tenant created successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
