<?php

namespace App\Http\Controllers\Central\Api;

use App\Enums\ActivationStatus;
use App\Enums\CompanySizes;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Setting;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function get_statistics()
    {
        try {
            $totalClients = Client::count();
            $activeClients = Client::where('status', 'active')->count();
            $inactiveClients = Client::where('status', 'inactive')->count();

            return ApiResponse([
                'total_clients' => $totalClients,
                'active_clients' => $activeClients,
                'inactive_clients' => $inactiveClients,
            ], 'Client statistics retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = Client::query();


            // // Search by contact name
            // if ($request->filled('search')) {
            //     $query->where('contact_name', 'like', '%' . $request->search . '%');
            // }

            // // Filter by status
            // if ($request->filled('status')) {
            //     $query->whereHas('subscription', fn($q) => $q->where('status', $request->status));
            // }

            // // Filter by max users
            // if ($request->filled('package')) {
            //     $query->where('max_users', '<=', $request->max_users);
            // }


            // // Filter by price range
            // if ($request->filled('price')) {
            //     $query->where('price', '<=', $request->price);
            // }

            // // Filter by duration unit
            // if ($request->filled('duration_unit')) {
            //     $query->where('duration_unit', $request->duration_unit);
            // }

            // // Filter by max users
            // if ($request->filled('max_users')) {
            //     $query->where('max_users', '<=', $request->max_users);
            // }

            // // Filter by min users
            // if ($request->filled('min_users')) {
            //     $query->where('max_users', '>=', $request->min_users);
            // }

            // // Filter by visibility
            // if ($request->filled('visibility')) {
            //     $query->where('visibility', $request->visibility);
            // }

            // // Filter by after date
            // if ($request->filled('after_date')) {
            //     $query->where('created_at', '>=', $request->after_date);
            // }

            // // Filter by before date
            // if ($request->filled('before_date')) {
            //     $query->where('created_at', '<=', $request->before_date);
            // }

            // // Filter by modules dropdown filter
            // if ($request->filled('modules')) {
            //     $query->where('modules', 'like', '%' . $request->modules . '%');
            // }

            // Get pagination per page from request or default to 10
            $perPage = $request->get('per_page', 10);

            // Paginate the results
            $clients = $query->paginate($perPage);

            return ApiResponse(new ClientCollection($clients), 'Clients retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $setting = Setting::first();
            $allowedIndustries = json_decode($setting?->industries ?? '[]', true);
            $validatedData = $request->validate([
                'company_name' => 'required|string|unique:clients',
                'subdomain' => 'required|string|unique:clients',
                'contact_name' => 'nullable|string|unique:clients',
                'contact_email' => 'required|email|unique:clients',
                'contact_phone' => 'required|string|unique:clients',
                'job_title' => 'nullable|string',
                'website' => 'nullable|string',
                'company_size' => ['nullable', Rule::in(CompanySizes::values())],
                'industry' => ['nullable', Rule::in($allowedIndustries)],
                'city_id' => 'nullable|exists:cities,id',
                'postal_code' => 'nullable|string',
                'address' => 'nullable|string',
                'status' => ['nullable', Rule::in(ActivationStatus::values())],
            ]);
            $client = Client::create($validatedData);
            return ApiResponse(new ClientResource($client), 'Client created successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $client)
    {
        try {
            $client = Client::findOrFail($client);
            $client->load(['subscriptions', 'area']);
            return ApiResponse(new ClientResource($client), 'Client retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'client not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function update(Request $request, Client $client)
    {
        try {
            $setting = Setting::first();
            $allowedIndustries = json_decode($setting?->industries ?? '[]', true);

            $validatedData = $request->validate([
                'company_name' => 'sometimes|string|unique:clients,company_name,' . $client->id,
                'subdomain' => 'sometimes|string|unique:clients,subdomain,' . $client->id,
                'contact_name' => 'sometimes|string|unique:clients,contact_name,' . $client->id,
                'contact_email' => 'sometimes|email|unique:clients,contact_email,' . $client->id,
                'contact_phone' => 'sometimes|string|unique:clients,contact_phone,' . $client->id,
                'job_title' => 'sometimes|string',
                'website' => 'sometimes|string',
                'company_size' => ['sometimes', Rule::in(CompanySizes::values())],
                'industry' => ['sometimes', Rule::in($allowedIndustries)],
                'city_id' => 'sometimes|exists:cities,id',
                'postal_code' => 'sometimes|string',
                'address' => 'sometimes|string',
                'status' => ['sometimes', Rule::in(ActivationStatus::values())],
            ]);

            $client->update($validatedData);
            return ApiResponse(new ClientResource($client), 'Client updated successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function destroy(int $client)
    {
        try {
            $client = Client::findOrFail($client);
            if ($client->subscriptions()->exists()) {
                return ApiResponse(message: 'Cannot delete client with active subscriptions', code: 400);
            }
            $client->delete();
            return ApiResponse(message: 'Client deleted successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'client not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
