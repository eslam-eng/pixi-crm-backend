<?php

namespace App\Http\Controllers\Central\Api;

use App\DTO\Central\ClientDTO;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Enums\Landlord\TenantStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\ClientRequest;
use App\Http\Resources\Central\ClientResource;
use App\Models\Central\Plan;
use App\Models\Central\Tenant;
use App\Services\Central\ClientService;
use App\Services\Central\SubscriptionService;
use Exception;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService $clientServie,
        private readonly SubscriptionService $subscriptionService
    ) {

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

    public function addTrialDays(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'days' => 'required|integer|min:1',
        ]);

        try {
            $tenant = Tenant::findOrFail($request->tenant_id);
            $plan = Plan::where('is_trial', true)->first();

            if (!$plan) {
                return ApiResponse(message: 'No trial plan found.', code: 404);
            }

            // Create trial subscription
            $tenant->subscriptions()->create([
                'plan_id' => $plan->id,
                'status' => SubscriptionStatusEnum::TRIAL,
                'starts_at' => now(),
                'ends_at' => now()->addDays((int) $request->days),
                'trial_ends_at' => now()->addDays((int) $request->days),
                'billing_cycle' => SubscriptionBillingCycleEnum::MONTHLY,
                'plan_snapshot' => $plan->toArray(),
                'amount' => 0,
            ]);

            // Update client status
            $tenant->update(['status' => TenantStatusEnum::TRIAL]);

            return ApiResponse(message: 'Trial days added successfully.');

        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function renewSubscription(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        try {
            $tenant = Tenant::findOrFail($request->tenant_id);
            $subscription = $tenant->latestSubscription;

            if (!$subscription) {
                return ApiResponse(message: 'No subscription found for this client.', code: 404);
            }

            // Renew the subscription (updates dates and creates invoice)
            $this->subscriptionService->renewManual($subscription);

            return ApiResponse(message: 'Subscription renewed successfully.');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
