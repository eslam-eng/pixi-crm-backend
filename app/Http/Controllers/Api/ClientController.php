<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\GeneralException;
use App\Http\Requests\Clients\UpdateClientRequest;
use App\Http\Requests\Clients\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\LocationCollection;
use Exception;
use Illuminate\Http\Request;
use App\DTO\Client\ClientDTO;
use App\Services\ClientService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;

class ClientController extends Controller
{
    public function __construct(public ClientService $clientService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page');
            $filters = array_filter($request->get('filters', []), function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            });
            $withRelations = ['area.ancestors', 'source'];
            $clients = $this->clientService->getClients($filters, $withRelations, $perPage);
            return ApiResponse(new ClientCollection($clients), 'Clients retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        try {
            DB::beginTransaction();
            $clientDTO = ClientDTO::fromRequest($request);
            $client = $this->clientService->store($clientDTO);
            DB::commit();
            return ApiResponse(new ClientResource($client), 'Client created successfully', code: 201);
        } catch (GeneralException $e) {
            return ApiResponse(message: $e->getMessage(), code: $e->getCode());
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(ClientUpdateRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        $clientDTO = ClientDTO::fromRequest($request);
        $client = $this->clientService->update($clientDTO, $id);
        DB::commit();
        return ApiResponse::sendResponse(200, 'Client updated successfully', new ClientResource($client));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
