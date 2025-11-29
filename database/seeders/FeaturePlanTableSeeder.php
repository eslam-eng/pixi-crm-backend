<?php

namespace Database\Seeders;

use App\Enums\Landlord\FeatureGroupEnum;
use App\Models\Central\Feature;
use App\Models\Central\FeaturePlan;
use App\Models\Central\Plan;
use Illuminate\Database\Seeder;

class FeaturePlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (FeaturePlan::count() > 0) {
            $this->command->info('Feature plans already exist. Skipping FeaturePlanTableSeeder.');
            return;
        }
        // Get all plans and features
        $plans = Plan::all();
        $features = Feature::all();

        foreach ($plans as $plan) {
            foreach ($features as $feature) {
                // For LIMIT type features, set a numeric value and not unlimited
                if ($feature->group === FeatureGroupEnum::LIMIT->value) {
                    FeaturePlan::factory()->create([
                        'plan_id' => $plan->id,
                        'feature_id' => $feature->id,
                        'value' => fake()->numberBetween(10, 1000),
                        'is_unlimited' => false,
                    ]);
                }
                // For FEATURE type, randomly set as unlimited or with value
                else {
                    $isUnlimited = fake()->boolean(70); // 70% chance of being unlimited
                    FeaturePlan::factory()->create([
                        'plan_id' => $plan->id,
                        'feature_id' => $feature->id,
                        'value' => $isUnlimited ? 1 : fake()->numberBetween(1, 10),
                        'is_unlimited' => $isUnlimited,
                    ]);
                }
            }
        }
    }
}
