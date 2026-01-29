<?php

namespace App\Services\Central;

use App\DTO\Central\UserDTO;
use App\Enums\Landlord\ActivationMethodEnum;
use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\InvoiceStatusEnum;
use App\Enums\Landlord\PaymentMethodEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Models\Central\Subscription;
use App\Services\Central\UserService;
use App\Services\Central\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Str;

class RegisterService
{
    public function __construct(
        protected UserService $userService,
        protected PlanService $planService,
        protected SubscriptionService $subscriptionService
    ) {
    }

    public function handle(UserDTO $registerDTO)
    {
        try {

            if ($registerDTO->activation_code) {
                $plan = $this->planService->findByActivationCode($registerDTO->activation_code);
            } elseif ($registerDTO->plan_id) {
                $plan = $this->planService->findById($registerDTO->plan_id);
            } elseif ($registerDTO->create_free_trial) {
                $plan = $this->planService->getFreePlan();
            }

            // 1. إنشاء المستخدم
            $user = $this->userService->getQuery()->create([
                'first_name' => $registerDTO->first_name,
                'last_name' => $registerDTO->last_name,
                'email' => $registerDTO->email,
                'password' => "123456",
                'job_title' => $registerDTO->job_title,
                'website' => $registerDTO->website,
                'city_id' => $registerDTO->city_id,
                'company_size' => $registerDTO->company_size,
                'industry_id' => $registerDTO->industry_id,
                'postal_code' => $registerDTO->postal_code,
                'address' => $registerDTO->address,
                'phone' => $registerDTO->phone,
            ]);

            // 2. إنشاء المستأجر (Tenant)
            $tenant = $user->tenant()->create([
                'id' => $registerDTO->domain,
                'name' => $registerDTO->domain,
                'tenancy_db_name' => 'crm_' . str_replace('.', '_', $registerDTO->domain) . '_' . mt_rand(1000, 9999),
                'tenancy_create_database' => true,
                'zapier_api_key' => 'zapier_' . Str::random(40),
            ]);

            // 3. إنشاء النطاق (Domain)
            $tenant->createDomain([
                'domain' => $registerDTO->domain,
            ]);

            return DB::connection('landlord')->transaction(function () use ($registerDTO, $tenant, $plan, $user) {
                // 4. حساب بداية الاشتراك
                $subscriptionStart = Carbon::parse($registerDTO->subscription_start);

                // 5. إنشاء الاشتراك والفاتورة والمميزات عبر SubscriptionService
                $subscription = $this->subscriptionService->store([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'billing_cycle' => $registerDTO->period_type,
                    'starts_at' => $subscriptionStart,
                    'auto_renew' => ActivationStatusEnum::INACTIVE->value,
                    'activation_method' => $registerDTO->activation_code ? ActivationMethodEnum::ACTIVATION_CODE->value : 'manual',
                    'activation_code' => $registerDTO->activation_code,
                    'invoice_status' => InvoiceStatusEnum::PAID->value,
                    'payment_method' => $registerDTO->activation_code ? PaymentMethodEnum::ACTIVATION_CODE->value : PaymentMethodEnum::CARD->value,
                ]);

                // 7. إنشاء مستخدم الأدمن في قاعدة بيانات المستأجر
                $tenant->run(function () use ($registerDTO, $user) {
                    $tenantUser = \App\Models\Tenant\User::create([
                        'first_name' => $registerDTO->first_name,
                        'last_name' => $registerDTO->last_name,
                        'email' => $registerDTO->email,
                        'password' => bcrypt("123456"),
                        'phone' => $registerDTO->phone,
                        'job_title' => $registerDTO->job_title,
                        'landlord_user_id' => $user->id,
                        'is_active' => true,
                    ]);

                    // إعطاء صلاحية الأدمن
                    try {
                        $tenantUser->assignRole('admin');
                    } catch (\Throwable $e) {
                        // Role might not exist if seeding hasn't run
                    }
                });

                // 8. إضافة المستخدم إلى جدول tenant_users في قاعدة بيانات اللاندلورد
                \App\Models\Central\TenantUser::create([
                    'tenant_id' => $tenant->id,
                    'email' => $registerDTO->email,
                    'name' => $registerDTO->first_name . ' ' . $registerDTO->last_name,
                ]);

                return $tenant;
            });
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
