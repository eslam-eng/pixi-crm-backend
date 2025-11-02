<?php

namespace Database\Factories\Central;

use App\Enums\Landlord\FeatureGroupEnum;
use App\Models\Central\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeatureFactory extends Factory
{
    protected $model = Feature::class;

    public function definition(): array
    {
        $name = [
            'en' => fake()->words(3, true),
            'ar' => 'ميزة '.fake()->words(2, true),
        ];

        return [
            'name' => $name,
            'description' => [
                'en' => fake()->sentence(10),
                'ar' => 'وصف '.fake()->sentence(8),
            ],
            'slug' => Str::slug($name['en']),
            'group' => fake()->randomElement(FeatureGroupEnum::values()),
            'is_active' => fake()->boolean(),
        ];
    }

    /**
     * Configure the feature as a limit type.
     */
    public function limit(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => FeatureGroupEnum::LIMIT->value,
        ]);
    }

    /**
     * Configure the feature as a feature type.
     */
    public function feature(): static
    {
        return $this->state(fn (array $attributes) => [
            'group' => FeatureGroupEnum::FEATURE->value,
        ]);
    }

    /**
     * Configure the feature as active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Configure the feature as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
