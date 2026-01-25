<?php

namespace App\Services\Central;

use App\DTO\Central\ClientDTO;
use App\DTO\Central\UserDTO;
use App\Enums\Landlord\ActivationMethodEnum;
use App\Notifications\Central\SetupPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Enums\Landlord\TenantStatusEnum;
use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\InvoiceStatusEnum;
use App\Enums\Landlord\PaymentMethodEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Models\Central\Filters\TenantFilters;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use App\Models\Central\TenantUser;
use App\Services\Central\BaseService;
use App\Services\Central\SubscriptionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClientService extends BaseService
{
    public function __construct(
        public UserService $userService,
        public PlanService $planService,
        public SubscriptionService $subscriptionService
    ) {
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
        return $this->getQuery(filters: $filters)->orderBy('created_at', 'desc')
            ->with(['owner', 'activeSubscription.plan', 'activeSubscription.activationCode'])
            ->paginate($perPage);
    }

    public function create(ClientDTO $clientDTO)
    {
        return DB::connection('landlord')->transaction(function () use ($clientDTO) {
            // 1. إنشاء المستخدم
            $user = $this->userService->create(UserDTO::fromArray([
                'first_name' => $clientDTO->first_name,
                'last_name' => $clientDTO->last_name,
                'company_name' => $clientDTO->company_name,
                'email' => $clientDTO->email,
                'password' => $clientDTO->password ?? '123456',
                'job_title' => $clientDTO->job_title,
                'website' => $clientDTO->website,
                'city_id' => $clientDTO->city_id,
                'company_size' => $clientDTO->company_size,
                'industry_id' => $clientDTO->industry_id,
                'postal_code' => $clientDTO->postal_code,
                'address' => $clientDTO->address,
                'phone' => $clientDTO->phone,
                'domain' => $clientDTO->domain,
            ]));

            // 2. إنشاء المستأجر (Tenant)
            $tenant = $user->tenant()->create([
                'id' => $clientDTO->domain,
                'name' => $clientDTO->domain,
                'status' => $clientDTO->status ?? TenantStatusEnum::ACTIVE->value,
                'tenancy_db_name' => $clientDTO->domain,
                'tenancy_create_database' => false,
            ]);

            // 3. إنشاء النطاق (Domain)
            $tenant->createDomain([
                'domain' => $clientDTO->domain,
            ]);

            // 4. إضافة المستخدم إلى جدول tenant_users
            TenantUser::create([
                'tenant_id' => $tenant->id,
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
            ]);

            // 5. إنشاء مستخدم الأدمن في قاعدة بيانات المستأجر
            $tenant->run(function () use ($clientDTO, $user) {
                $tenantUser = \App\Models\Tenant\User::create([
                    'first_name' => $clientDTO->first_name,
                    'last_name' => $clientDTO->last_name,
                    'email' => $clientDTO->email,
                    'password' => bcrypt($clientDTO->password ?? "123456"),
                    'phone' => $clientDTO->phone,
                    'job_title' => $clientDTO->job_title,
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

            // 6. إنشاء الاشتراك والفاتورة والمميزات عبر SubscriptionService
            $this->subscriptionService->store([
                'tenant_id' => $tenant->id,
                'plan_id' => $clientDTO->plan_id,
                'billing_cycle' => $clientDTO->period_type,
                'starts_at' => $clientDTO->subscription_start,
                'auto_renew' => ActivationStatusEnum::INACTIVE->value,
                'activation_method' => ActivationMethodEnum::MANUAL_ACTIVATION->value,
                'invoice_status' => InvoiceStatusEnum::PAID->value,
                'payment_method' => PaymentMethodEnum::ACTIVATION_CODE->value,
            ]);

            if ($clientDTO->send_password_setup_email) {
                $token = Str::random(60);
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'token' => $token,
                        'created_at' => now(),
                    ]
                );
                $user->notify(new SetupPasswordNotification($token, $user->email));
            }

            return $tenant;
        });
    }
}
