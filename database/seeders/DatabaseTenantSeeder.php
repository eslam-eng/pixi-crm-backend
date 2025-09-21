<?php

namespace Database\Seeders;

use Database\Seeders\Tenant\AttributeWithValueProductSeeder;
use Database\Seeders\Tenant\ContactSeeder;
use Database\Seeders\Tenant\DepartmentSeeder;
use Database\Seeders\Tenant\ItemCategorySeeder;
use Database\Seeders\Tenant\ItemSeeder;
use Database\Seeders\Tenant\UserSeeder;
use Database\Seeders\Tenant\OpportunitySeeder;
use Database\Seeders\Tenant\PaymentMethodSeeder;
use Database\Seeders\Tenant\PipelineSeeder;
use Database\Seeders\Tenant\ProductWithVariantSeeder;
use Database\Seeders\Tenant\RolesAndPermissionsSeeder;
use Database\Seeders\Tenant\RoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseTenantSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // RolesAndPermissionsSeeder::class,
            RoleSeeder::class,
            CountriesWithCitiesSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            SourceSeeder::class,
            PipelineSeeder::class,
            ContactSeeder::class,
            ItemCategorySeeder::class,
            ItemSeeder::class,
            // PaymentMethodSeeder::class,
            AttributeWithValueProductSeeder::class,
            ProductWithVariantSeeder::class,
            OpportunitySeeder::class,
            // ItemStatusSeeder::class,
            // ItemCategorySeeder::class,
            // AppSettingsSeeder::class,
        ]);
    }
}
