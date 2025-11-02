<?php

namespace Database\Factories\Central;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Models\Central\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => [
                'ar' => fake()->slug(3),
                'en' => fake()->slug(3),
                'fr' => fake()->slug(3),
                'sp' => fake()->slug(3),
            ],
            'description' => [
                'ar' => fake()->sentence(10),
                'en' => fake()->sentence(10),
            ],
            'monthly_price' => fake()->numberBetween(30, 50),
            'annual_price' => fake()->numberBetween(30, 50),
            'lifetime_price' => fake()->numberBetween(30, 50),
            'is_active' => fake()->randomElement(ActivationStatusEnum::values()),
            'trial_days' => 14,  // Common trial period of 14 days
            'sort_order' => fake()->numberBetween(1, 100),
            'currency_code' => 'USD',  // Default currency
            'refund_days' => fake()->numberBetween(0, 30),  // Common refund period up to 30 days
        ];
    }
}
