<?php

namespace App\Http\Controllers\Web;


use App\Models\Client;
use App\Models\Service;
use App\Models\Industry;
use App\Models\Location;
use App\Models\Resource;
use App\Models\CustomField;
use Illuminate\Http\Request;
use App\DTO\Client\ClientDTO;
use App\Services\ClientService;
use Illuminate\Support\Facades\DB;
use App\DataTables\ClientsDataTable;
use App\Http\Controllers\Controller;
use App\Exceptions\NotFoundException;
use App\Http\Requests\Clients\StoreClientRequest;
use App\Http\Requests\Clients\UpdateClientRequest;


class ClientController extends Controller
{
    public function __construct(public ClientService $clientService) {
        $this->middleware('permission:view clients', ['only' => ['index', 'show']]);
        $this->middleware('permission:create clients', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit clients', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete clients', ['only' => ['destroy']]);
    }

    public function index(ClientsDataTable $dataTable, Request $request)
    {
        $filters = array_filter($request->get('filters', []), function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $withRelations = ['client']; // Include the 'client' relationship
        return $dataTable->with(['filters' => $filters, 'withRelations' => $withRelations])->render('layouts.dashboard.client.index');
    }

    public function create()
    {
        // Fetch related data for form (e.g., sources, industries, clients, custom fields)
        $sources = Resource::all();
        $industries = Industry::all();
        $clients = Client::all();
        $customFields = CustomField::all();
        $cities = Location::where('status', 1)->get();
        $services = Service::with('categories')->get();
        return view('layouts.dashboard.client.create', compact('sources', 'industries', 'clients', 'customFields', 'services','cities'));
    }

    public function store(StoreClientRequest $request)
    {
        try {
            DB::beginTransaction();
            // Create ClientDTO from the request
            $clientDTO = ClientDTO::fromRequest($request);
            // Store the client using the service
            $client = $this->clientService->store($clientDTO);
            $toast = [
                'type' => 'success',
                'title' => 'Success',
                'message' => trans('app.client_created_successfully')
            ];
            DB::commit();
            return to_route('clients.index')->with('toast', $toast);
        } catch (\Exception $e) {
            DB::rollBack();
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage()
            ];
            return back()->with('toast', $toast);
        }
    }

    public function edit( $id)
    {
        // Fetch related data for editing
        $sources = Resource::all();
        $industries = Industry::all();
        $clients = Client::all();
        $customFields = CustomField::all();
        $services = Service::all();
        $cities = Location::where('status', 1)->get();
        $client = $this->clientService->findById(id: $id);
        return view('layouts.dashboard.client.edit', compact('client','cities', 'sources', 'industries', 'clients', 'customFields','services'));
    }

    public function update(UpdateClientRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            // Create ClientDTO from the request
            $clientDTO = ClientDTO::fromRequest($request);
            // Update the client using the client
            $client = $this->clientService->update($id, $clientDTO);
            // Success message
            $toast = [
                'type' => 'success',
                'title' => 'Success',
                'message' => trans('app.client_updated_successfully')
            ];
            DB::commit();
            return to_route('clients.index')->with('toast', $toast);
        } catch (\Exception $e) {
            DB::rollBack();
            // Error message
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage()
            ];
            return back()->with('toast', $toast);
        }
    }

    public function show(int $id)
    {
        try {
            $withRelations = ['resource', 'industries', 'services', 'customFields'];
            $client = $this->clientService->findById(id: $id, withRelations: $withRelations);

            return view('layouts.dashboard.client.show', compact('client'));
        } catch (NotFoundException $e) {
            $toast = [
                'type' => 'error',
                'title' => 'Error',
                'message' => $e->getMessage()
            ];
            return to_route('clients.index')->with('toast', $toast);
        }
    }


    public function destroy(int $id)
    {
        try{
            $this->clientService->delete($id);
            $toast = [
                'type' => 'success',
                'title' => 'success',
                'message' => trans('app.client_deleted_successfully')
            ];
            return to_route('clients.index')->with('toast', $toast);
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
