<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Clear the table before seeding
        // Location::truncate();

        // Seed countries
        $egypt = Location::create([
            'title' => 'Egypt',
            'status' => 'active',
        ]);

        $saudiArabia = Location::create([
            'title' => 'Saudi Arabia',
            'status' => 'active',
        ]);

        $unitedStates = Location::create([
            'title' => 'United States',
            'status' => 'active',
        ]);

        $france = Location::create([
            'title' => 'France',
            'status' => 'active',
        ]);

        // Seed cities under Egypt
        $cairo = Location::create([
            'title' => 'Cairo',
            'status' => 'active',
            'parent_id' => $egypt->id,
        ]);

        $alexandria = Location::create([
            'title' => 'Alexandria',
            'status' => 'active',
            'parent_id' => $egypt->id,
        ]);

        $luxor = Location::create([
            'title' => 'Luxor',
            'status' => 'active',
            'parent_id' => $egypt->id,
        ]);

        // Seed areas under Cairo
        Location::create([
            'title' => 'Maadi',
            'status' => 'active',
            'parent_id' => $cairo->id,
        ]);

        Location::create([
            'title' => 'Nasr City',
            'status' => 'active',
            'parent_id' => $cairo->id,
        ]);

        Location::create([
            'title' => 'Heliopolis',
            'status' => 'active',
            'parent_id' => $cairo->id,
        ]);

        // Seed areas under Alexandria
        Location::create([
            'title' => 'Montaza',
            'status' => 'active',
            'parent_id' => $alexandria->id,
        ]);

        Location::create([
            'title' => 'Sidi Gaber',
            'status' => 'active',
            'parent_id' => $alexandria->id,
        ]);

        // Seed cities under Saudi Arabia
        $riyadh = Location::create([
            'title' => 'Riyadh',
            'status' => 'active',
            'parent_id' => $saudiArabia->id,
        ]);

        $jeddah = Location::create([
            'title' => 'Jeddah',
            'status' => 'active',
            'parent_id' => $saudiArabia->id,
        ]);

        $dammam = Location::create([
            'title' => 'Dammam',
            'status' => 'active',
            'parent_id' => $saudiArabia->id,
        ]);

        // Seed areas under Riyadh
        Location::create([
            'title' => 'Al Olaya',
            'status' => 'active',
            'parent_id' => $riyadh->id,
        ]);

        Location::create([
            'title' => 'Al Malaz',
            'status' => 'active',
            'parent_id' => $riyadh->id,
        ]);

        Location::create([
            'title' => 'Dirab',
            'status' => 'active',
            'parent_id' => $riyadh->id,
        ]);

        // Seed areas under Jeddah
        Location::create([
            'title' => 'Al Hamra',
            'status' => 'active',
            'parent_id' => $jeddah->id,
        ]);

        Location::create([
            'title' => 'Al Salamah',
            'status' => 'active',
            'parent_id' => $jeddah->id,
        ]);

        // Seed cities under United States
        $newYork = Location::create([
            'title' => 'New York',
            'status' => 'active',
            'parent_id' => $unitedStates->id,
        ]);

        $losAngeles = Location::create([
            'title' => 'Los Angeles',
            'status' => 'active',
            'parent_id' => $unitedStates->id,
        ]);

        $chicago = Location::create([
            'title' => 'Chicago',
            'status' => 'active',
            'parent_id' => $unitedStates->id,
        ]);

        // Seed areas under New York
        Location::create([
            'title' => 'Manhattan',
            'status' => 'active',
            'parent_id' => $newYork->id,
        ]);

        Location::create([
            'title' => 'Brooklyn',
            'status' => 'active',
            'parent_id' => $newYork->id,
        ]);

        Location::create([
            'title' => 'Queens',
            'status' => 'active',
            'parent_id' => $newYork->id,
        ]);

        // Seed areas under Los Angeles
        Location::create([
            'title' => 'Hollywood',
            'status' => 'active',
            'parent_id' => $losAngeles->id,
        ]);

        Location::create([
            'title' => 'Santa Monica',
            'status' => 'active',
            'parent_id' => $losAngeles->id,
        ]);

        // Seed cities under France
        $paris = Location::create([
            'title' => 'Paris',
            'status' => 'active',
            'parent_id' => $france->id,
        ]);

        $lyon = Location::create([
            'title' => 'Lyon',
            'status' => 'active',
            'parent_id' => $france->id,
        ]);

        $marseille = Location::create([
            'title' => 'Marseille',
            'status' => 'active',
            'parent_id' => $france->id,
        ]);

        // Seed areas under Paris
        Location::create([
            'title' => 'Eiffel Tower Area',
            'status' => 'active',
            'parent_id' => $paris->id,
        ]);

        Location::create([
            'title' => 'Louvre Museum Area',
            'status' => 'active',
            'parent_id' => $paris->id,
        ]);

        Location::create([
            'title' => 'Montmartre',
            'status' => 'active',
            'parent_id' => $paris->id,
        ]);
    }
}
