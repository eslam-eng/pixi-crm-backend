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
            ['name' => 'kanban-leads', 'group' => 'leads', 'description' => 'Kanban leads'],
            ['name' => 'change-stage', 'group' => 'leads', 'description' => 'Change stage'],
            ['name' => 'get-activities-list', 'group' => 'leads', 'description' => 'Get activities list'],

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
            ['name' => 'toggle-forms', 'group' => 'forms', 'description' => 'Toggle forms'],

            // Deal Management
            ['name' => 'view-deals', 'group' => 'deals', 'description' => 'View deals'],
            ['name' => 'create-deals', 'group' => 'deals', 'description' => 'Create deals'],
            ['name' => 'edit-deals', 'group' => 'deals', 'description' => 'Edit deals'],
            ['name' => 'delete-deals', 'group' => 'deals', 'description' => 'Delete deals'],
            ['name' => 'change-approval-status', 'group' => 'deals', 'description' => 'Change approval status'],

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

            // Attendance Management
            ['name' => 'view-attendance', 'group' => 'attendance', 'description' => 'View attendance'],
            ['name' => 'create-attendance', 'group' => 'attendance', 'description' => 'Create attendance'],

            // Settings
            ['name' => 'manage-settings', 'group' => 'settings', 'description' => 'Manage system settings'],

            // Admin dashboard
            ['name' => 'view-admin-dashboard', 'group' => 'dashboard', 'description' => 'View admin dashboard'],
            // Manager dashboard
            ['name' => 'view-manager-dashboard', 'group' => 'dashboard', 'description' => 'View manager dashboard'],
            // Agent dashboard
            ['name' => 'view-agent-dashboard', 'group' => 'dashboard', 'description' => 'View agent dashboard'],
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

        $admin->syncPermissions(Permission::all()->reject(function ($permission) {
            return in_array($permission->name, ['view-manager-dashboard', 'view-agent-dashboard']);
        }));
        $manager->syncPermissions(Permission::all()->reject(function ($permission) {
            return in_array($permission->name, ['view-admin-dashboard', 'view-agent-dashboard']);
        }));
        $agent->syncPermissions(Permission::all()->reject(function ($permission) {
            return in_array($permission->name, ['view-admin-dashboard', 'view-manager-dashboard']);
        }));

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
