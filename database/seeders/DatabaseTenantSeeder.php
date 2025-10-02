<?php

namespace Database\Seeders;

use Database\Seeders\Tenant\AttributeWithValueProductSeeder;
use Database\Seeders\Tenant\ContactSeeder;
use Database\Seeders\Tenant\DealSeeder;
use Database\Seeders\Tenant\DepartmentSeeder;
use Database\Seeders\Tenant\ItemCategorySeeder;
use Database\Seeders\Tenant\ItemSeeder;
use Database\Seeders\Tenant\UserSeeder;
use Database\Seeders\Tenant\OpportunitySeeder;
use Database\Seeders\Tenant\PaymentMethodSeeder;
use Database\Seeders\Tenant\PipelineSeeder;
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
            RoleSeeder::class,
            CountriesWithCitiesSeeder::class,
            AttributeWithValueProductSeeder::class,
            ItemCategorySeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            SourceSeeder::class,
            PipelineSeeder::class,
            ContactSeeder::class,
            ItemSeeder::class,
            PaymentMethodSeeder::class,
            OpportunitySeeder::class,
            DealSeeder::class,
            // ProductWithVariantSeeder::class,
            // ItemStatusSeeder::class,
            // ItemCategorySeeder::class,
            // AppSettingsSeeder::class,
        ]);
    }
}
