<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\ClientDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\ClientRequest;
use App\Http\Resources\Central\ClientResource;
use App\Services\Central\ClientService;
use Exception;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(private readonly ClientService $clientServie)
    {

    }

    public function index(Request $request)
    {
        try {
            $client = $this->clientServie->paginate($request->all());
            $data = ClientResource::collection($client)->response()->getData(true);
            return ApiResponse($data, 'Clients retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(ClientRequest $request)
    {
        try {
            $clientDTO = ClientDTO::fromRequest($request);
            $this->clientServie->create($clientDTO);
            return ApiResponse(message: 'Tenant created successfully');
        } catch (\Throwable $e) {
            return ApiResponse(
                message: 'Tenant creation failed: ' . $e,
                code: 500
            );
        }
    }

    public function show(string $id)
    {
        try {
            $tenant = $this->clientServie->findById($id);
            $data = new ClientResource($tenant);
            return ApiResponse(data: $data, message: 'Tenant retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
