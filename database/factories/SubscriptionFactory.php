<?php

namespace Database\Factories;

use App\Enums\landlord\ActivitionMethods;
use App\Enums\landlord\PaymentStatus;
use App\Models\Client;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Tier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

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

        $client = Client::inRandomOrder()->first();
        $tier = Tier::inRandomOrder()->first();
        return [
            'client_id' => $client?->id,
            'tier_id' => $tier?->id,
            'subscription_start_date' => now(),
            'subscription_end_date' => now()->addDays($tier?->duration),
            'subscription_status' => fake()->randomElement(['active', 'inactive']),
            'activition_method' => fake()->randomElement(ActivitionMethods::values()),
            'source' => $allowedSources[array_rand($allowedSources)],
            'payment_status' => fake()->randomElement(PaymentStatus::values()),
            'auto_renew' => fake()->randomElement(['yes', 'no']),
        ];
    }
}
