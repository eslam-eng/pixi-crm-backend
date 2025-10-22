<?php

namespace App\Services\Tenant\Users;

use App\DTO\Tenant\TenantUserDTO;
use App\Models\Tenant\Filters\UsersFilter;
use App\Models\Tenant\Role;
use App\Models\Tenant\User;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTenantUserService extends BaseService
{
    /**
     * Return the filter class for users.
     */
    protected function getFilterClass(): string
    {
        return UsersFilter::class;
    }

    /**
     * Return the base query for users.
     */
    protected function baseQuery(): Builder
    {
        return User::query();
    }

    public function handle(TenantUserDTO $tenantUserDTO)
    {
        // check if tenant has availability to create user
        $currentLandlordForAuthUser = $tenantUserDTO->landlordUser;
        $tenant = $currentLandlordForAuthUser->tenant;

        $this->startTransaction();

        //        $tenant->consumeFeature(slug: 'max-users');

        // create landlord user if not exists;
        $landlordUser = $currentLandlordForAuthUser;
        if (! $currentLandlordForAuthUser) {
            $random_password = $this->generateRandomPassword($tenantUserDTO->email);
            $landlordUser = $this->createLandlordUser(dto: $tenantUserDTO, password: $random_password, landlordUser: $currentLandlordForAuthUser);
        }

        //        $tenant->users()->attach($landlordUser->id);

        $tenantUserDTO->landlordUser = $landlordUser;

        $tenantUser = $this->baseQuery()->create($tenantUserDTO->toArray());

        $role = is_int($tenantUserDTO->role) ? Role::query()->find($tenantUserDTO->role) : $tenantUserDTO->role;

        $tenantUser->assignRole($role->name);

        $this->commitTransaction();

        if ($tenantUserDTO->send_credential_email) {
            $this->sendCredentialsEmail($tenantUser, $random_password);
        }

        return $tenantUser;
    }

    private function generateRandomPassword($emailOrName): string
    {
        $base = strtolower(preg_replace('/[^a-z]/i', '', $emailOrName));
        $base = substr($base, 0, 5); // Take first 5 characters max
        $random_number = substr(preg_replace('/\D/', '', Str::uuid()), 0, 5);

        return $base.$random_number;
    }

    private function createLandlordUser(TenantUserDTO $dto, $password, \App\Models\Landlord\User $landlordUser)
    {
        // validate
        return \App\Models\Landlord\User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'email_verified_at' => now(),
            'tenant_id' => $landlordUser->tenan_id,
            'password' => bcrypt($password),
        ]);
    }

    private function sendCredentialsEmail(User $user, string $random_password)
    {
        // Send email
        //            Mail::to($user->email)->queue(new UserCredentialsMail(
        //                user: $user,
        //                password: $random_password,
        //                loginUrl: config('app.frontend_login_url'), //todo get it from config and env files for react project
        //            ));
    }

    private function startTransaction(): void
    {
        DB::connection('landlord')->beginTransaction();
        DB::connection('tenant')->beginTransaction();
    }

    private function commitTransaction(): void
    {
        DB::connection('tenant')->commit();
        DB::connection('landlord')->commit();
    }
}
