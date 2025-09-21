<?php

namespace Database\Seeders\tenant;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Source::create([
            'name' => 'Website',
        ]);
        Source::create([
            'name' => 'Cold Call',
        ]);
        Source::create([
            'name' => 'Trade Show',
        ]);
        Source::create([
            'name' => 'Advertisement',
        ]);
    }
}