<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\ItemStatus;
use Illuminate\Database\Seeder;

class ItemStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ItemStatus::factory()->count(10)->create();
    }
}
