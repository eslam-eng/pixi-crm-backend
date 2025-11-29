<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Models\Central\Plan;
use App\Models\Central\Subscription;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Tenant::count() > 0) {
            $this->command->info('Tenants already exist. Skipping TenantSeeder.');
            return;
        }
        // Tenant::create([
        //     'name' => 'pixicrm', // unique ID
        //     'domains' => ['pixicrm'], // subdomain,
        // ]);


        if (config('app.env') == 'development') {


            // $databaseName = 'barmagiat_crm_tenant';
            $databaseName = 'barmagiat_crm_tenant';

            // check if database already exists
            $exists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            $acmeTenant = Tenant::create([
                'id' => 'pixicrm',
                'name' => 'pixicrm',
                'tenancy_db_name' => $databaseName,
                'tenancy_create_database' => !$exists, // only create if not exists
            ]);

            $acmeTenant->createDomain([
                'domain' => 'pixicrm',
            ]);

            $plan = Plan::trial()->first();

            if (!$plan) {
                logger()->error('No trial plans found. Please create plans with trial_days > 0 first.');
                throw new \RuntimeException('No trial plans found. Please create plans with trial_days > 0 first.');
            }


            $tenants = Tenant::all();
            foreach ($tenants as $tenant) {
                // Randomly select a trial plan
                $planSnapshot = $plan->only($plan->getFillable());

                $planSnapshot['name'] = $plan->getTranslations('name');
                // Create subscription for the tenant
                $subscription = Subscription::query()->create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'status' => SubscriptionStatusEnum::ACTIVE->value,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($plan->trial_days),
                    'trial_ends_at' => now()->addDays($plan->trial_days),
                    'billing_cycle' => SubscriptionBillingCycleEnum::MONTHLY->value,
                    'auto_renew' => ActivationStatusEnum::INACTIVE->value,
                    'plan_snapshot' => json_encode($planSnapshot),
                    'amount' => $plan->monthly_price,
                ]);

                // 3. Snapshot plan features and limits to pivot table
                $allFeatures = $plan->features; // Assuming this returns all features (including limits)

                $snapshot = [];

                foreach ($allFeatures as $feature) {
                    $pivotData = $feature->pivot?->value ?? null;

                    if (!$pivotData) {
                        continue;
                    }

                    $snapshot[$feature->id] = [
                        'value' => $pivotData,
                        'slug' => $feature->slug,
                        'name' => json_encode($feature->getTranslations('name')),
                        'group' => $feature->group,
                    ];
                }
                // 4. Attach to feature_plan_subscription pivot
                $subscription->features()->attach($snapshot);
            }
            // $tenant = Tenant::create(
            //     [
            //         'name' => 'pixicrm'
            //     ]
            // );

            // $tenant->createDomain([
            //     'domain' => 'pixicrm',
            // ]);
        }
    }
}
