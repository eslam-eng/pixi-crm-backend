<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Client;
use App\Models\Source;
use App\Models\Location;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $setting = Setting::first();
        $allowedSources = json_decode($setting?->sources ?? '[]', true);
        $allowedIndustries = json_decode($setting?->industries ?? '[]', true);

        return [
            'company_name' => fake()->company(),
            'contact_phone' => fake()->phoneNumber(),
            'contact_email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'city_id' => City::inRandomOrder()->first()?->id,
            'subdomain' => fake()->unique()->safeEmail(),
            'contact_name' => fake()->name(),
            'job_title' => fake()->jobTitle(),
            'website' => fake()->url(),
            'company_size' => fake()->randomElement(['1 - 10 employees', '11 - 50 employees', '51 - 200 employees', '201 - 500 employees', '501 - 1000 employees', '1000+ employees', null]),
            'industry' => fake()->randomElement($allowedIndustries),
            'postal_code' => fake()->postcode(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'notes' => fake()->text(),
        ];
    }

    /**
     * Configure the factory with specific source.
     */
    public function withSource(Source $source): Factory
    {
        return $this->state(function (array $attributes) use ($source) {
            return [
                'source_id' => $source->id,
            ];
        });
    }

    /**
     * Configure the factory with specific area.
     */
    public function withArea(Location $area): Factory
    {
        return $this->state(function (array $attributes) use ($area) {
            return [
                'area_id' => $area->id,
            ];
        });
    }
}
