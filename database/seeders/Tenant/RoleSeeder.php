<?php

namespace Database\Seeders\Tenant;

use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Role::count() == 0) {
            $roles = [
                ['name' => RolesEnum::ADMIN->value, 'guard_name' => 'api_tenant', 'created_at' => now(), 'updated_at' => now()],
                ['name' => RolesEnum::MANAGER->value, 'guard_name' => 'api_tenant', 'created_at' => now(), 'updated_at' => now()],
                ['name' => RolesEnum::AGENT->value, 'guard_name' => 'api_tenant', 'created_at' => now(), 'updated_at' => now()],
            ];

            Role::insert($roles);
        }
    }
}
