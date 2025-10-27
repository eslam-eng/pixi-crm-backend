<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // UserSeeder::class,
            // SettingsSeeder::class,
            // CountriesWithCitiesSeeder::class,
            // ClientSeeder::class,
            // PackageSeeder::class,
            // SubscriptionSeeder::class,
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            TenantSeeder::class,
            AdminTableSeeder::class,
            // ModuleSeeder::class,
            // PermissionSeeder::class,
            // PermissionRoleSeeder::class,
            // RoleSeeder::class,
            // LocationsTableSeeder::class,

        ]);
    }
}
