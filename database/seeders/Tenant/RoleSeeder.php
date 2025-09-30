<?php

namespace Database\Seeders\Tenant;

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

        // Create permissions with groups
        $permissions = [
            // Lead Management
            ['name' => 'view-leads', 'group' => 'leads', 'description' => 'View all leads'],
            ['name' => 'create-leads', 'group' => 'leads', 'description' => 'Create new leads'],
            ['name' => 'edit-leads', 'group' => 'leads', 'description' => 'Edit all leads'],
            ['name' => 'delete-leads', 'group' => 'leads', 'description' => 'Delete leads'],

            // Contact Management
            ['name' => 'view-contacts', 'group' => 'contacts', 'description' => 'View contacts'],
            ['name' => 'create-contacts', 'group' => 'contacts', 'description' => 'Create contacts'],
            ['name' => 'edit-contacts', 'group' => 'contacts', 'description' => 'Edit contacts'],
            ['name' => 'delete-contacts', 'group' => 'contacts', 'description' => 'Delete contacts'],
            ['name' => 'merge-contacts', 'group' => 'contacts', 'description' => 'Merge contacts'],

            // Form Management
            ['name' => 'view-forms', 'group' => 'forms', 'description' => 'View forms'],
            ['name' => 'create-forms', 'group' => 'forms', 'description' => 'Create forms'],
            ['name' => 'edit-forms', 'group' => 'forms', 'description' => 'Edit forms'],
            ['name' => 'delete-forms', 'group' => 'forms', 'description' => 'Delete forms'],

            // Deal Management
            ['name' => 'view-deals', 'group' => 'deals', 'description' => 'View deals'],
            ['name' => 'create-deals', 'group' => 'deals', 'description' => 'Create deals'],
            ['name' => 'edit-deals', 'group' => 'deals', 'description' => 'Edit deals'],
            ['name' => 'delete-deals', 'group' => 'deals', 'description' => 'Delete deals'],

            // Task Management
            ['name' => 'view-tasks', 'group' => 'tasks', 'description' => 'View tasks'],
            ['name' => 'create-tasks', 'group' => 'tasks', 'description' => 'Create tasks'],
            ['name' => 'edit-tasks', 'group' => 'tasks', 'description' => 'Edit tasks'],
            ['name' => 'delete-tasks', 'group' => 'tasks', 'description' => 'Delete tasks'],

            // Item Management
            ['name' => 'view-items', 'group' => 'items', 'description' => 'View items'],
            ['name' => 'create-items', 'group' => 'items', 'description' => 'Create items'],
            ['name' => 'edit-items', 'group' => 'items', 'description' => 'Edit items'],
            ['name' => 'delete-items', 'group' => 'items', 'description' => 'Delete items'],

            // User Management
            // ['name' => 'view-users', 'group' => 'users', 'description' => 'View users'],
            // ['name' => 'create-users', 'group' => 'users', 'description' => 'Create users'],
            // ['name' => 'edit-users', 'group' => 'users', 'description' => 'Edit users'],
            // ['name' => 'delete-users', 'group' => 'users', 'description' => 'Delete users'],

            // Role & Permission Management
            // ['name' => 'view-roles', 'group' => 'roles', 'description' => 'View roles'],
            // ['name' => 'create-roles', 'group' => 'roles', 'description' => 'Create roles'],
            // ['name' => 'edit-roles', 'group' => 'roles', 'description' => 'Edit roles'],
            // ['name' => 'delete-roles', 'group' => 'roles', 'description' => 'Delete roles'],

            // Settings
            ['name' => 'manage-settings', 'group' => 'settings', 'description' => 'Manage system settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'api_tenant'],
                $permission
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

        $admin->syncPermissions(Permission::all());
        $manager->syncPermissions(Permission::all());
        $agent->syncPermissions(Permission::all());

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
