<?php

namespace App\Services\Landlord\Actions\Auth;

use App\DTO\Central\TenantDTO;
use App\DTO\Tenant\TenantUserDTO;
use App\DTO\Central\UserDTO;
use App\Events\UserRegistered;
use App\Models\Tenant;

use App\Models\User;
use App\Services\Landlord\Actions\Subscription\FreeTrialService;
use App\Services\Landlord\Plan\PlanService;
use App\Services\Landlord\Subscription\SubscriptionService;
use App\Services\Landlord\Tenant\TenantService;
use App\Services\Landlord\UserService;
use App\Services\Tenant\Users\CreateTenantUserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterService
{
    private ?Tenant $createdTenant = null;

    /**
     * Inject UsersService via constructor.
     */
    public function __construct(
        protected UserService $userService,
        protected CreateTenantUserService $createTenantUserService,
        protected TenantService $tenantService,
        protected PlanService $planService,
        protected SubscriptionService $planSubscriptionService,
        protected readonly FreeTrialService $freeTrialService
    ) {}

    /**
     * @throws \Throwable
     */
    public function handle(UserDTO $registerDTO): User
    {
        try {
            return DB::connection('landlord')->transaction(function () use ($registerDTO) {
                $user = $this->registerUserWithTenant($registerDTO);
                event(new UserRegistered(user: $user, create_free_trial: $registerDTO->create_free_trial, activation_code: $registerDTO->activation_code));

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
            name: $registerDTO->organization_name,
            slug: Str::slug($registerDTO->organization_name)
        );

        return $this->tenantService->create($tenantDTO);
    }

    private function createUser(UserDTO $registerDTO, Tenant $tenant): User
    {
        $registerDTO->tenant_id = $tenant->id;

        $landlordUser = $this->userService->create($registerDTO);

        $role = Role::query()->where('name', Role::ADMIN)->first();

        $tenantUserDTO = new TenantUserDTO(
            name: $registerDTO->name,
            email: $registerDTO->email,
            role: $role,
            password: $registerDTO->password,
            landlordUser: $landlordUser,
            email_verified_at: null,
            send_credential_email: false,
        );

        $this->createTenantUserService->handle($tenantUserDTO);
        //        $tenant->users()->attach($landlordUser->id, ['is_owner' => true]);
        $tenant->update(['owner_id' => $landlordUser->id]);

        return $landlordUser;
    }

    // Drop the tenant database if needed
    private function dropTenantDatabase(string $databaseName): void
    {
        DB::statement("DROP DATABASE IF EXISTS `$databaseName`");
    }
}
