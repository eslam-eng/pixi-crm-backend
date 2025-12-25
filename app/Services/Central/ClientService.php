<?php

namespace App\Services\Central;

use App\DTO\Central\ClientDTO;
use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\InvoiceStatusEnum;
use App\Enums\Landlord\PaymentMethodEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Models\Central\Filters\TenantFilters;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use App\Services\Central\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClientService extends BaseService
{
    public function __construct(
        public UserService $userService,
        public PlanService $planService
    )
    {
    }

    /**
     * Return the filter class for users.
     */
    protected function getFilterClass(): string
    {
        return TenantFilters::class;
    }

    /**
     * Return the base query for users.
     */
    protected function baseQuery(): Builder
    {
        return Tenant::query();
    }

    public function paginate(?array $filters = [], int $perPage = 15)
    {
        return $this->getQuery(filters: $filters)->orderBy('id', 'desc')
            ->with(['owner'])
            ->paginate($perPage);
    }

    public function create(ClientDTO $clientDTO)
    {
        // 1. إنشاء المستخدم
        $user = $this->userService->getQuery()->create([
            'first_name' => $clientDTO->contact_name,
            'last_name'  => $clientDTO->contact_name,
            'email'      => $clientDTO->contact_email,
            'password'   => bcrypt('123456') // لا تنسى التشفير
        ]);

        // 2. إنشاء المستأجر (Tenant)
        $tenant = $user->tenant()->create([
            'id'                      => $clientDTO->subdomain,
            'name'                    => $clientDTO->subdomain,
            'tenancy_db_name'         => $clientDTO->subdomain,
            'tenancy_create_database' => false,
        ]);

        // 3. إنشاء النطاق (Domain)
        $tenant->createDomain([
            'domain' => $clientDTO->subdomain,
        ]);

        return DB::transaction(function () use ($clientDTO, $tenant, $user) { 
            // 4. حساب السعر والمدة
            $plan = $this->planService->findById($clientDTO->package_id);
            
            $amount = match ($clientDTO->period_type) {
                SubscriptionBillingCycleEnum::MONTHLY->value => $plan->monthly_price,
                SubscriptionBillingCycleEnum::ANNUAL->value  => $plan->annual_price,
                SubscriptionBillingCycleEnum::LIFETIME->value => $plan->lifetime_price,
                default => 0,
            };

            $subscriptionStart = Carbon::parse($clientDTO->subscription_start);
            $ends_at = match ($clientDTO->period_type) {
                SubscriptionBillingCycleEnum::MONTHLY->value => $subscriptionStart->copy()->addMonth(),
                SubscriptionBillingCycleEnum::ANNUAL->value  => $subscriptionStart->copy()->addYear(),
                default => null,
            };
            
            $finalEndsAt = $ends_at ? $ends_at->addSecond()->format('Y-m-d H:i:s') : null;
            // 5. إنشاء الاشتراك
            $subscription = Subscription::create([
                'tenant_id'     => $tenant->id,
                'plan_id'       => $plan->id,
                'status'        => SubscriptionStatusEnum::ACTIVE->value,
                'starts_at'     => $subscriptionStart,
                'ends_at'       => $finalEndsAt,
                'trial_ends_at' => $subscriptionStart->copy()->addDays($plan->refund_days),
                'billing_cycle' => $clientDTO->period_type,
                'auto_renew'    => ActivationStatusEnum::INACTIVE->value,
                'plan_snapshot' => json_encode($plan->only($plan->getFillable())),
                'amount'        => $amount,
            ]);

            // 6. إنشاء الفاتورة
            $subscription->invoices()->create([
                'tenant_id'           => $tenant->id,
                'subtotal'            => $amount,
                'tax_amount'          => 0,
                'discount_percentage' => 0,
                'total'               => $amount,
                'status'              => InvoiceStatusEnum::PAID->value,
                'paid_at'             => now(),
                'payment_method'      => PaymentMethodEnum::ACTIVATION_CODE->value,
            ]);

            return $tenant;
        });
    }

    private function creationRollBack(
        $tenant = null,
        $user = null, 
        $subscription = null, 
        $invoice = null): void
    {
        if ($tenant) {
            // 🔥 stancl handles domains + DB deletion safely
            $tenant->delete();
        }
        if ($user) {
            $user->delete();
        }
        if ($invoice) {
            $invoice->delete();
        }
        if ($subscription) {
            $subscription->delete();
        }
    }
}
