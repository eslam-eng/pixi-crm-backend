<?php

namespace Database\Factories\Tenant;

use App\Models\Tenant\ItemStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\ItemStatus>
 */
class ItemStatusFactory extends Factory
{
    protected $model = ItemStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'color' => fake()->colorName(),
            'status' => fake()->boolean(),
        ];
    }
}
