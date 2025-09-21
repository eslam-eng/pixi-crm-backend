<?php

namespace App\Http\Controllers\Central\Api;

use App\Enums\CompanySizes;
use App\Enums\landlord\ActivitionMethods;
use App\Enums\landlord\InvoiceStatus;
use App\Enums\landlord\PaymentStatus;
use App\Enums\landlord\SubscriptionsStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Subscribe\SubscribeRequest;
use App\Http\Resources\LandlordSubscription\SubscriptionCollection;
use App\Http\Resources\LandlordSubscription\SubscriptionResource;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Tier;
use App\Services\AuthService;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Subscription::query();

            // Search by package name
            if ($request->filled('search')) {
                $query->whereHas('client', function ($query) use ($request) {
                    $query->where('company_name', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by subscription_status
            if ($request->filled('subscription_status')) {
                $query->where('subscription_status', $request->subscription_status);
            }

            // Filter by package_name in package table
            if ($request->filled('package_name')) {
                $query->whereHas('tier', function ($query) use ($request) {
                    $query->where('package_name', $request->package_name);
                });
            }

            // Filter by payment_status
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }


            // Filter by max users
            if ($request->filled('activition_method')) {
                $query->where('activition_method', $request->activition_method);
            }

            // Filter by min users
            if ($request->filled('subscription_start_date')) {
                $query->where('subscription_start_date', '>=', $request->subscription_start_date);
            }

            // Filter by min users
            if ($request->filled('subscription_end_date')) {
                $query->where('subscription_end_date', '>=', $request->subscription_end_date);
            }

            // Filter by auto_renew
            if ($request->filled('auto_renew')) {
                $query->where('auto_renew', $request->auto_renew);
            }

            // Filter by package_cycle
            if ($request->filled('package_cycle')) {
                $query->whereHas('tier', function ($query) use ($request) {
                    $query->where('duration_unit', $request->package_cycle);
                });
            }



            // Filter by after date
            if ($request->filled('after_date')) {
                $query->where('created_at', '>=', $request->after_date);
            }

            // Filter by before date
            if ($request->filled('before_date')) {
                $query->where('created_at', '<=', $request->before_date);
            }

            // Filter by modules dropdown filter
            if ($request->filled('modules')) {
                $query->where('modules', 'like', '%' . $request->modules . '%');
            }

            // Get pagination per page from request or default to 10
            $perPage = $request->get('per_page', 10);


            // Paginate the results
            $subscriptions = $query->with('client', 'tier')->paginate($perPage);
            return ApiResponse(new SubscriptionCollection($subscriptions), 'Subscriptions retrieved successfully');
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $setting = Setting::first();
            $allowedSources = json_decode($setting?->sources ?? '[]', true);

            $validatedData = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'tier_id' => 'required|exists:tiers,id',
                'activition_method' => ['required', Rule::in(ActivitionMethods::values())],
                'source' => ['required', Rule::in($allowedSources)],
                'subscription_status' => ['required', Rule::in(SubscriptionsStatus::values())],
                'auto_renew' => ['required', Rule::in(['yes', 'no'])],
                'payment_status' => ['required', Rule::in(PaymentStatus::values())],
                'note' => 'nullable|string',
            ]);


            $subscription = Subscription::create($validatedData);

            if ($request->hasFile('file')) {
                $subscription->addMediaFromRequest('file')->toMediaCollection();
            }

            $client = Client::find($validatedData['client_id']);
            $tier = Tier::find($validatedData['tier_id']);

            $tenant = Tenant::create([
                'id' => $client->subdomain,
                'tenancy_db_name' => "crm_" . $client->subdomain,
                'name' => $client->company_name,
                'client_id' => $client->id
            ]);

            $tenant->createDomain([
                'domain' => $client->subdomain,
            ]);

            $tenant->tiers()->sync($tier->id);
            $tenant->save();

            tenancy()->initialize($tenant);

            app()->make(AuthService::class)->signup(
                first_name: $client->contact_name,
                last_name: $client->contact_name,
                email: $client->contact_email,
                password: Hash::make("123456"),
                type: "admin"
            );

            DB::commit();

            return ApiResponse(new SubscriptionResource($subscription), 'Subscription created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function show(int $subscription)
    {
        try {
            $subscription = Subscription::findOrFail($subscription);
            $subscription->load(['client', 'tier']);
            return ApiResponse(new SubscriptionResource($subscription), 'Subscription retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Subscription not found', code: 404);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function subscribe(SubscribeRequest $request)
    {
        try {
            DB::beginTransaction();

            $tier = Tier::find($request['tier_id']);

            $billable = Client::create([
                'company_name' => $request['company_name'],
                'subdomain' => $request['subdomain'],
                'contact_name' => $request['contact_name'],
                'contact_email' => $request['contact_email'],
                'contact_phone' => $request['contact_phone'],
                'job_title' => $request['job_title'],
                'website' => $request['website'],
                'company_size' => $request['company_size'],
                'industry' => $request['industry'],
                'city_id' => $request['city_id'],
                'postal_code' => $request['postal_code'],
                'address' => $request['address'],
            ]);

            $subscription = Subscription::create([
                'client_id' => $billable->id,
                'tier_id' => $request['tier_id'],
                'subscription_start_date' => now(),
                'subscription_end_date' => now()->addDays($tier->duration),
                'subscription_status' => SubscriptionsStatus::ACTIVE,
                'activation_method' => $request['activation_method'] ?? ActivitionMethods::MANUAL,
                'source' => $request['source'] ?? null,
            ]);

            $invoice = Invoice::create([
                'client_id' => $billable->id,
                'subscription_id' => $subscription->id,
                'amount' => $tier->price,
                'status' => InvoiceStatus::PENDING,
                'due_date' => $subscription->subscription_end_date,
            ]);

            // Create or get Stripe customer
            if (!$billable->hasStripeId()) {
                $billable->createAsStripeCustomer([
                    'name' => $billable->company_name ?? $billable->name,
                    'email' => $billable->contact_email ?? $billable->email,
                ]);
            }

            // Add payment method
            $billable->addPaymentMethod($request->payment_method);
            $billable->updateDefaultPaymentMethod($request->payment_method);

            // Create subscription
            $subscription = $billable->newSubscription('default', $tier->price)
                ->create($request->payment_method);

            DB::commit();
            return ApiResponse($subscription, 'Subscription created successfully');
        } catch (IncompletePayment $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment requires additional confirmation',
                'payment_intent' => [
                    'id' => $exception->payment->id,
                    'client_secret' => $exception->payment->client_secret,
                ]
            ], 402);
        } catch (\Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }

    public function getActivationMethod()
    {
        $activationMethods = ActivitionMethods::values();
        return ApiResponse($activationMethods, 'Activation methods retrieved successfully');
    }

    public function getPaymentStatus()
    {
        $paymentStatus = PaymentStatus::values();
        return ApiResponse($paymentStatus, 'Payment status retrieved successfully');
    }

    public function getSubscriptionStatus()
    {
        $subscriptionStatus = SubscriptionsStatus::values();
        return ApiResponse($subscriptionStatus, 'Subscription status retrieved successfully');
    }

    public function update(int $subscription, Request $request): JsonResponse
    {
        $subscription = Subscription::findOrFail($subscription);
        // Update the subscription
        $validatedData = $request->validate([
            'client_id' => 'sometimes|exists:clients,id',
            'tier_id' => 'sometimes|exists:tiers,id',
            'activition_method' => ['sometimes', Rule::in(ActivitionMethods::values())],
            'source' => ['sometimes', Rule::in(json_decode(Setting::first()?->sources ?? '[]', true))],
            'subscription_status' => ['sometimes', Rule::in(SubscriptionsStatus::values())],
            'subscription_start_date' => ['sometimes', 'date'],
            'subscription_end_date' => ['sometimes', 'date'],
            'auto_renew' => ['sometimes', Rule::in(['yes', 'no'])],
            'payment_status' => ['sometimes', Rule::in(PaymentStatus::values())],
            'note' => 'nullable|string',
        ]);

        $subscription->update($validatedData);


        return ApiResponse($subscription, 'Subscription updated successfully');
    }

    public function destroy(int $subscription)
    {
        try {
            $subscription = Subscription::findOrFail($subscription);
            $subscription->delete();
            return ApiResponse(message: 'Subscription deleted successfully');
        } catch (ModelNotFoundException $e) {
            return ApiResponse(message: 'Subscription not found', code: 404);
        } catch (Exception $e) {
            return ApiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
