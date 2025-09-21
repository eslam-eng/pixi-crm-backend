<?php

namespace Database\Seeders;

use Database\Seeders\tenant\AttributeWithValueProductSeeder;
use Database\Seeders\tenant\ContactSeeder;
use Database\Seeders\tenant\DepartmentSeeder;
use Database\Seeders\tenant\ItemCategorySeeder;
use Database\Seeders\tenant\ItemSeeder;
use Database\Seeders\tenant\UserSeeder;
use Database\Seeders\tenant\OpportunitySeeder;
use Database\Seeders\tenant\PaymentMethodSeeder;
use Database\Seeders\tenant\PipelineSeeder;
use Database\Seeders\tenant\ProductWithVariantSeeder;
use Database\Seeders\tenant\RolesAndPermissionsSeeder;
use Database\Seeders\tenant\RoleSeeder;
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
