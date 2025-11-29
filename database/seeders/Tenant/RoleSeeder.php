<?php

namespace Database\Seeders\Tenant;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions from PermissionsEnum
        foreach (PermissionsEnum::cases() as $permissionEnum) {
            Permission::firstOrCreate(
                ['name' => $permissionEnum->value, 'guard_name' => 'api_tenant'],
                [
                    'name' => $permissionEnum->value,
                    'group' => $permissionEnum->getGroup(),
                    'description' => $permissionEnum->getDescription(),
                ]
            );
        }

        $admin = Role::firstOrCreate(
            ['name' => RolesEnum::ADMIN->value, 'guard_name' => 'api_tenant'],
            [
                'description' => 'Administrator with most permissions',
                'is_system' => true,
            ]
        );

        $manager = Role::firstOrCreate(
            ['name' => RolesEnum::MANAGER->value, 'guard_name' => 'api_tenant'],
            [
                'description' => 'Sales Manager',
                'is_system' => true,
            ]
        );

        $agent = Role::firstOrCreate(
            ['name' => RolesEnum::AGENT->value, 'guard_name' => 'api_tenant'],
            [
                'description' => 'Sales Representative',
                'is_system' => true,
            ]
        );

        $admin->syncPermissions(Permission::all()->reject(function ($permission) {
            return in_array($permission->name, [
                PermissionsEnum::VIEW_MANAGER_DASHBOARD->value,
                PermissionsEnum::VIEW_AGENT_DASHBOARD->value
            ]);
        }));
        $manager->syncPermissions(Permission::all()->reject(function ($permission) {
            return in_array($permission->name, [
                PermissionsEnum::VIEW_ADMIN_DASHBOARD->value,
                PermissionsEnum::VIEW_AGENT_DASHBOARD->value
            ]);
        }));
        $agent->syncPermissions(Permission::all()->reject(function ($permission) {
            return in_array($permission->name, [
                PermissionsEnum::VIEW_ADMIN_DASHBOARD->value,
                PermissionsEnum::VIEW_MANAGER_DASHBOARD->value
            ]);
        }));

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
