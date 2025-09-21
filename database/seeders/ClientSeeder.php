<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Source;
use App\Models\Location;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory()->count(2)->create();
    }
}
