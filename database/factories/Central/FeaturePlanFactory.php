<?php

namespace Database\Factories\Central;

use App\Models\Central\Feature;
use App\Models\Central\FeaturePlan;
use App\Models\Central\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeaturePlanFactory extends Factory
{
    protected $model = FeaturePlan::class;

    public function definition(): array
    {
        return [
            'plan_id' => Plan::factory(),
            'feature_id' => Feature::factory(),
            'value' => fake()->numberBetween(1, 1000),
            'is_unlimited' => fake()->boolean(),
        ];
    }

    /**
     * Configure the feature plan as unlimited.
     */
    public function unlimited(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_unlimited' => true,
            'value' => null,
        ]);
    }

    /**
     * Configure the feature plan with a specific value.
     */
    public function withValue(int $value): static
    {
        return $this->state(fn (array $attributes) => [
            'is_unlimited' => false,
            'value' => $value,
        ]);
    }

    /**
     * Configure the feature plan for a specific plan.
     */
    public function forPlan(Plan $plan): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_id' => $plan->id,
        ]);
    }

    /**
     * Configure the feature plan for a specific feature.
     */
    public function forFeature(Feature $feature): static
    {
        return $this->state(fn (array $attributes) => [
            'feature_id' => $feature->id,
        ]);
    }
}
