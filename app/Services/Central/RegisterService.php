<?php

namespace App\Services\Central;

use App\DTO\Central\TenantDTO;
use App\DTO\Central\TenantUserDTO;
use App\DTO\Central\UserDTO;
use App\Enums\RolesEnum;
use App\Events\UserRegistered;
use App\Models\Central\Tenant;
use App\Models\Central\User;
use App\Models\User as TenantUser;
use App\Services\Central\Subscription\FreeTrialService;
use App\Services\Central\Plan\PlanService;
use App\Services\Central\Subscription\SubscriptionService;
use App\Services\Central\TenantService;
use App\Services\Central\UserService;
use App\Services\Central\User\CreateTenantUserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RegisterService
{
    private ?Tenant $createdTenant = null;

    /**
     * Inject UsersService via constructor.
     */
    public function __construct(
        protected UserService $userService,
        protected CreateTenantUserService $createTenantUserService,
        // protected TenantService $tenantService,
        // protected PlanService $planService,
        // protected SubscriptionService $planSubscriptionService,
        // protected readonly FreeTrialService $freeTrialService
    ) {}

    /**
     * @throws \Throwable
     */
    public function handle(UserDTO $registerDTO): User
    {
        try {
            return DB::connection('landlord')->transaction(function () use ($registerDTO) {
                $user = $this->registerUserWithTenant($registerDTO);
                // event(new UserRegistered(user: $user, create_free_trial: $registerDTO->create_free_trial, activation_code: $registerDTO->activation_code));

                return $user;
            });
        } catch (\Throwable $e) {
            // If tenant was created, drop its database
            if ($this->createdTenant && $this->createdTenant->database) {
                $this->dropTenantDatabase($this->createdTenant->database);
            }
            throw $e;
        }
    }

    private function registerUserWithTenant(UserDTO $registerDTO): ?User
    {
        $tenant = $this->createTenantFromDTO($registerDTO);

        $this->createdTenant = $tenant; // Track for cleanup
        // create landlord user
        $user = $this->createUser($registerDTO, $tenant);

        return $user->fresh();
    }

    private function createTenantFromDTO(UserDTO $registerDTO): Tenant
    {
        $tenantDTO = new TenantDTO(
            tenant_id: $registerDTO->organization_name,
            name: $registerDTO->organization_name,
            // tenancy_db_name: Str::slug($registerDTO->organization_name),
        );
        $tenant = Tenant::create($tenantDTO->toArray());
        $tenant->createDomain([
            'domain' => $registerDTO->organization_name,
        ]);
        return $tenant;
    }

    private function createUser(UserDTO $registerDTO, Tenant $tenant): User
    {
        $registerDTO->tenant_id = $tenant->id;
        // $landlordUser = $this->userService->create($registerDTO);
        $landlordUser = $tenant->user();

        $role = Role::query()->where('name', RolesEnum::ADMIN->value)->first();

        // $tenantUserDTO = new TenantUserDTO(
        //     name: $registerDTO->name,
        //     email: $registerDTO->email,
        //     role: $role,
        //     password: $registerDTO->password,
        //     landlordUser: $landlordUser,
        //     email_verified_at: null,
        //     send_credential_email: false,
        // );


        // $this->createTenantUserService->handle($tenantUserDTO);
        //        $tenant->users()->attach($landlordUser->id, ['is_owner' => true]);
        $tenant->update(['owner_id' => $landlordUser->id]);

        $tenant->run(function () use ($registerDTO) {
            return TenantUser::create([
                'first_name' => $registerDTO->name,
                'last_name' => $registerDTO->name,
                'email' => $registerDTO->email,
                'password' => bcrypt($registerDTO->password),
                'lang' => 'en',
            ]);
        });

        return $landlordUser;
    }

    // Drop the tenant database if needed
    private function dropTenantDatabase(string $databaseName): void
    {
        DB::statement("DROP DATABASE IF EXISTS `$databaseName`");
    }
}
