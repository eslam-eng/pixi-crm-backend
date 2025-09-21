<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CountriesWithCitiesSeeder extends Seeder
{
    public function run(): void
    {
        // Only run if no countries exist
        if (Country::count() > 0) {
            $this->command->info('Countries already exist. Skipping CountriesWithCitiesSeeder.');
            return;
        }
   
        $jsonPath = database_path('data/countries_with_cities.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('countries_with_cities.json file not found.');
            return;
        }

        $countries = json_decode(File::get($jsonPath), true);

        foreach ($countries as $country) {
            $countryId = DB::table('countries')->insertGetId([
                'name' => $country['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cities = collect($country['cities'])
                ->map(fn($name) => $this->cleanCityName($name))
                ->filter()
                ->unique()
                ->take(5) // ✅ Only insert first 10 unique, cleaned cities
                ->map(fn($name) => [
                    'name' => $name,
                    'country_id' => $countryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->values()
                ->toArray();

            if (!empty($cities)) {
                DB::table('cities')->insert($cities);
            }
        }
    }

    private function cleanCityName(string $name): string
    {
        return Str::of($name)
            ->replace(['‘', '’', '“', '”', '"', "'", '`'], '')
            ->trim()
            ->value();
    }
}
