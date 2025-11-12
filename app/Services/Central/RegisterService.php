<?php

namespace App\Services\Central;

use App\DTO\Central\TenantDTO;
use App\DTO\Central\TenantUserDTO;
use App\DTO\Central\UserDTO;
use App\Enums\RolesEnum;
use App\Events\UserRegistered;
use App\Models\Central\Tenant;
use App\Models\Central\User as LandlordUser;
use App\Models\Tenant\User as TenantUser;
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
    public function handle(UserDTO $registerDTO): array
    {
        try {
            return DB::connection('landlord')->transaction(function () use ($registerDTO) {
                [$user, $token] = $this->registerUserWithTenant($registerDTO);
                event(new UserRegistered(user: $user, create_free_trial: $registerDTO->create_free_trial, activation_code: $registerDTO->activation_code));
                return [
                    'user' => $user,
                    'token' => $token,
                ];
            });
        } catch (\Throwable $e) {
            dd($e);
            // If tenant was created, drop its database
            if ($this->createdTenant) {
                $databaseName = $this->createdTenant->database()->getName();
                if ($databaseName) {
                    $this->dropTenantDatabase($databaseName);
                }
                $this->createdTenant->delete();
            }
            throw $e;
        }
    }

    private function registerUserWithTenant(UserDTO $registerDTO): array
    {
        $tenant = $this->createTenantFromDTO($registerDTO);
        $this->createdTenant = $tenant; // Track for cleanup
        // create tenant user
        $landlordUser = $this->createUser($registerDTO, $tenant);

        $token = $landlordUser->generateToken('auth_token');
        return [$landlordUser, $token];
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

    private function createUser(UserDTO $registerDTO, Tenant $tenant): LandlordUser
    {
        $registerDTO->tenant_id = $tenant->id;
        $landlordUser = LandlordUser::create([
            'first_name' => $registerDTO->name,
            'last_name' => $registerDTO->name,
            'email' => $registerDTO->email,
            'password' => bcrypt($registerDTO->password),
            'lang' => 'en',
        ]);
        $tenant->update(['owner_id' => $landlordUser->id]);

        // $tenantUser = $tenant->run(function () use ($registerDTO, $landlordUser) {
        //     $role = Role::query()->where('name', RolesEnum::ADMIN->value)->first();
        //     $tenantUser = TenantUser::create([
        //         'first_name' => $registerDTO->name,
        //         'last_name' => $registerDTO->name,
        //         'email' => $registerDTO->email,
        //         'password' => bcrypt($registerDTO->password),
        //         'lang' => 'en',
        //         'landlord_user_id' => $landlordUser->id,
        //     ]);
        //     $tenantUser->assignRole($role);
        //     return $tenantUser;
        // });
        return $landlordUser;
    }

    // Drop the tenant database if needed
    private function dropTenantDatabase(string $databaseName): void
    {
        DB::statement("DROP DATABASE IF EXISTS `$databaseName`");
    }
}
