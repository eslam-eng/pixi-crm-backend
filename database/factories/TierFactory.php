<?php

namespace Database\Factories;

use App\Enums\DurationUnits;
use App\Enums\ModuleType;
use App\Models\Tier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tier>
 */
class TierFactory extends Factory
{
    protected $model = Tier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'package_name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 9.99, 999.99),
            'duration_unit' => fake()->randomElement(DurationUnits::values()),
            'duration' => fake()->numberBetween(1, 365),
            'refund_period' => fake()->numberBetween(1, 30),
            'max_users' => fake()->numberBetween(1, 100),
            'max_contacts' => fake()->numberBetween(100, 10000),
            'storage_limit' => fake()->numberBetween(1, 1000),
            'modules' => fake()->randomElements(ModuleType::values(), fake()->numberBetween(1, 3)),
            'status' => fake()->randomElement(['active', 'inactive']),
            'availability' => fake()->randomElement(['Public', 'Private']),
        ];
    }

    /**
     * Configure the factory for a basic tier.
     */
    public function basic(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'package_name' => 'Basic',
                'price' => 9.99,
                'max_users' => 5,
                'max_contacts' => 1000,
                'storage_limit' => 10,
                'modules' => [
                    ModuleType::CRM_CORE->value,
                    ModuleType::NOTIFICATIONS->value,
                ],
                'status' => 'active',
                'availability' => 'Public',
            ];
        });
    }

    /**
     * Configure the factory for a professional tier.
     */
    public function professional(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'package_name' => 'Professional',
                'price' => 29.99,
                'max_users' => 25,
                'max_contacts' => 5000,
                'storage_limit' => 100,
                'modules' => [
                    ModuleType::CRM_CORE->value,
                    ModuleType::LEAD_MANAGEMENT->value,
                    ModuleType::REPORTS_ANALYTICS->value,
                    ModuleType::NOTIFICATIONS->value,
                ],
                'status' => 'active',
                'availability' => 'Public',
            ];
        });
    }

    /**
     * Configure the factory for an enterprise tier.
     */
    public function enterprise(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'package_name' => 'Enterprise',
                'price' => 99.99,
                'max_users' => null, // Unlimited
                'max_contacts' => 50000,
                'storage_limit' => 1000,
                'modules' => ModuleType::values(), // All modules
                'status' => 'active',
                'availability' => 'Public',
            ];
        });
    }
}
