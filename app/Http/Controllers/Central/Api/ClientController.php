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
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientService $clientService,
        private readonly SubscriptionService $subscriptionService,
        private readonly \App\Services\Central\ActivationCode\ActivationCodeService $activationCodeService
    ) {

    }

    public function index(Request $request)
    {
        try {
            $client = $this->clientService->paginate($request->all());
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
            $this->clientService->create($clientDTO);
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
            $tenant = $this->clientService->findById($id);
            $data = new ClientResource($tenant);
            return ApiResponse(data: $data, message: 'Tenant retrieved successfully');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
    public function addActivationCode(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'activation_code' => 'required|string|exists:activation_codes,code'
        ]);

        try {
            // Check if code is active, not used (AVAILABLE), and not expired
            $activationCode = $this->activationCodeService->checkActivationCode($request->activation_code);

            if (!$activationCode) {
                return ApiResponse(message: 'Invalid, used, or expired activation code.', code: 422);
            }

            $plan = Plan::findOrFail($activationCode->plan_id);
            $data = [
                'tenant_id' => $request->tenant_id,
                'activation_code' => $request->activation_code,
                'activation_method' => \App\Enums\Landlord\ActivationMethodEnum::ACTIVATION_CODE->value,
                'plan_id' => $plan->id,
                'billing_cycle' => $activationCode->billing_cycle ?? SubscriptionBillingCycleEnum::LIFETIME->value
            ];


            $this->subscriptionService->store($data);

            // Mark Activation Code as Used
            $activationCode->update(['status' => \App\Enums\Landlord\ActivationCodeStatusEnum::USED->value]);

            // Update tenant status to Active
            $tenant = Tenant::findOrFail($request->tenant_id);
            $tenant->update(['status' => TenantStatusEnum::ACTIVE->value]);

            return ApiResponse(message: 'Activation code applied successfully.');

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
                'status' => SubscriptionStatusEnum::TRIAL->value,
                'starts_at' => now(),
                'ends_at' => now()->addDays((int) $request->days),
                'trial_ends_at' => now()->addDays((int) $request->days),
                'billing_cycle' => SubscriptionBillingCycleEnum::MONTHLY->value,
                'plan_snapshot' => $plan->toArray(),
                'amount' => 0,
            ]);


            // Update client status
            $tenant->update(['status' => TenantStatusEnum::TRIAL->value]);

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

    public function changeStatus(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        try {
            $tenant = Tenant::findOrFail($request->tenant_id);

            // If currently Active (1) or Trial (3), make Inactive (0)
            if (in_array($tenant->status->value, [TenantStatusEnum::ACTIVE->value, TenantStatusEnum::TRIAL->value])) {
                $tenant->update(['status' => TenantStatusEnum::INACTIVE->value]);
                $message = 'Client deactivated successfully.';
            } else {
                // Otherwise (Inactive 0, Expired 4), switch to Active/Trial
                $targetStatus = TenantStatusEnum::ACTIVE->value;

                // Check for valid trial subscription
                $hasActiveTrial = $tenant->subscriptions()
                    ->where('status', SubscriptionStatusEnum::TRIAL->value)
                    ->where(function ($query) {
                        $query->whereNull('ends_at')
                            ->orWhere('ends_at', '>', now());
                    })
                    ->exists();

                if ($hasActiveTrial) {
                    $targetStatus = TenantStatusEnum::TRIAL->value;
                }

                $tenant->update(['status' => $targetStatus]);
                $message = 'Client activated successfully.';
            }

            return ApiResponse(message: $message);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
    public function sendPasswordReset(Request $request): JsonResponse
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        try {
            $this->clientService->sendPasswordReset($request->tenant_id);

            return ApiResponse(message: 'Password reset link sent successfully.');
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
