<?php

namespace App\Services\Landlord;

use App\DTOs\Landlord\AdminDTO;
use App\Mail\UserCredentialsMail;
use App\Models\Landlord\Admin;
use App\Models\Landlord\Filters\AdminsFilter;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminService extends BaseService
{
    public function __construct(protected readonly RoleService $roleService) {}

    /**
     * Return the filter class for users.
     */
    protected function getFilterClass(): string
    {
        return AdminsFilter::class;
    }

    /**
     * Return the base query for users.
     */
    protected function baseQuery(): Builder
    {
        return Admin::query();
    }

    public function paginate(?array $filters = [], int $perPage = 15)
    {
        return $this->getQuery(filters: $filters)
            ->with(['roles'])
            ->paginate($perPage);
    }

    public function create(AdminDTO $adminDTO)
    {
        return DB::connection('landlord')->transaction(function () use ($adminDTO) {
            $random_password = $this->generateRandomPassword($adminDTO->email);
            $adminDTO->password = $random_password;
            $adminDTO->email_verified_at = now();
            $admin = $this->baseQuery()
                ->create($adminDTO->toArray());

            $roles_names = $this->roleService->getRolesNameByIds($adminDTO->role_ids);
            $admin->syncRoles($roles_names);

            //            $this->sendCredentialsEmail($admin, $random_password);

            return $admin;
        });
    }

    public function update(Admin|int $admin, AdminDTO $adminDTO): void
    {
        DB::connection('landlord')->transaction(function () use ($admin, $adminDTO) {
            if (is_int($admin)) {
                $admin = $this->findById($admin);
            }

            $admin->update($adminDTO->toArrayExcept(['password', 'email_verified_at']));

            $roles_names = $this->roleService->getRolesNameByIds($adminDTO->role_ids);

            $admin->syncRoles($roles_names);
        });
    }

    public function delete(Admin|int $admin): ?bool
    {
        if (is_int($admin)) {
            $admin = $this->findById($admin);
        }

        return $admin->delete();
    }

    private function generateRandomPassword($emailOrName): string
    {
        $base = strtolower(preg_replace('/[^a-z]/i', '', $emailOrName));
        $base = substr($base, 0, 5); // Take first 5 characters max
        $random_number = substr(preg_replace('/\D/', '', Str::uuid()), 0, 5);

        return $base.$random_number;
    }

    private function sendCredentialsEmail(Admin $admin, string $random_password): void
    {
        // Send email
        Mail::to($admin->email)->queue(new UserCredentialsMail(
            user: $admin,
            password: $random_password,
            //            loginUrl: config('app.frontend_login_url'), //todo get it from config and env files for react project
        ));
    }
}
