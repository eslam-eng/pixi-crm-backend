<?php

namespace App\Http\Controllers\Central\Api\Subscription;

use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\RenewSubscriptionRequest;
use App\Http\Requests\Central\SubscriptionRequest;
use App\Http\Requests\Central\UpgradeSubscriptionRequest;
use App\Http\Resources\Central\SubscriptionResource;
use App\Models\Central\DiscountCode;
use App\Services\Central\Stripe\StripeService;
use App\Services\Central\Subscription\RenewSubscriptionService;
use App\Services\Central\Subscription\SubscriptionManager;
use App\Services\Central\Subscription\UpgradeSubscriptionService;
use App\Services\Central\Subscription\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
        private readonly SubscriptionManager $subscriptionManager,
        private readonly RenewSubscriptionService $renewSubscriptionService,
        private readonly UpgradeSubscriptionService $upgradeSubscriptionService,
        // private readonly StripeService $stripeService,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->all();
        $limit = $request->input('limit', 15);

        // If authenticated as a normal user (not landlord admin), force tenant filter
        if (auth('sanctum')->check() && ! auth('landlord')->check()) {
            $filters['tenant_id'] = auth('sanctum')->user()->tenant_id;
            $filters['sort_by_latest_active'] = true;
            $filters['active'] = true;
        }
        $subscriptions = $this->subscriptionService->paginate(filters: $filters, perPage: $limit);

        return SubscriptionResource::collection($subscriptions);
    }

    public function statics()
    {
        $stats = $this->subscriptionService->statics();

        return ApiResponse::success(data: $stats);
    }

    public function subscribe(SubscriptionRequest $request)
    {
        $user = auth()->user();

        $invoice = $this->subscriptionManager->createPaidSubscription([
            'plan_id' => $request->plan_id,
            'duration_type' => $request->duration_type,
            'discountCode' => DiscountCode::query()->firstWhere('discount_code', $request->discount_code),
        ], $user);

        $payIntent = $this->stripeService->pay($invoice);
        $invoice->update(['payment_reference' => $payIntent->id]);

        return ApiResponse::success(data: [
            'client_secret' => $payIntent->client_secret,
        ], message: 'Subscription created successfully,please confirm payment');
    }

    public function renew(string $current_subscription_id, RenewSubscriptionRequest $request)
    {
        $invoice = $this->renewSubscriptionService->handle(currentSubscription: $current_subscription_id, discount_code: $request->discount_code);
        $payIntent = $this->stripeService->pay($invoice);

        return ApiResponse::success(data: $payIntent, message: 'Subscription renewed successfully,please confirm payment');
    }

    public function upgrade(UpgradeSubscriptionRequest $upgradeSubscriptionRequest)
    {
        $billingCycle = SubscriptionBillingCycleEnum::from($upgradeSubscriptionRequest->billing_cycle);
        $user = auth()->user();
        $tenant = $user->tenant;
        $invoice = $this->upgradeSubscriptionService->handle(newPlan: $upgradeSubscriptionRequest->new_plan_id, subscriptionDurationEnum: $billingCycle, discount_code: $upgradeSubscriptionRequest->discount_code, tenant: $tenant);
        $payIntent = $this->stripeService->pay($invoice);

        return ApiResponse::success(data: $payIntent, message: 'Subscription upgraded successfully,please confirm payment');
    }

    public function subscriptionInvoices($subscription_id)
    {
        $subscription = $this->subscriptionService->findById($subscription_id, withRelation: ['invoices.tenant']);

        return SubscriptionResource::make($subscription);
    }
}
