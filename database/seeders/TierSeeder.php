<?php

namespace Database\Seeders;

use App\Enums\ModuleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Basic tier with essential features',
                'price' => 9.99,
                'modules' => [
                    ModuleType::USER_MANAGEMENT->value,
                    ModuleType::NOTIFICATIONS->value,
                    ModuleType::SETTINGS->value,
                ],
                'max_users' => 5,
                'storage_limit_gb' => 10,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Professional tier with advanced features',
                'price' => 29.99,
                'modules' => [
                    ModuleType::USER_MANAGEMENT->value,
                    ModuleType::INVENTORY->value,
                    ModuleType::REPORTING->value,
                    ModuleType::NOTIFICATIONS->value,
                    ModuleType::SETTINGS->value,
                ],
                'max_users' => 25,
                'storage_limit_gb' => 100,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Enterprise tier with all features',
                'price' => 99.99,
                'modules' => ModuleType::values(), // All modules
                'max_users' => null, // Unlimited
                'storage_limit_gb' => 1000,
            ],
        ];

        foreach ($tiers as $tierData) {
            Tier::create($tierData);
        }
    }
}
