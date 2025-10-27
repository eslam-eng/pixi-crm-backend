<?php

namespace Database\Seeders\Tenant;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Source::count() > 0) {
            $this->command->info('Sources already exist. Skipping SourceSeeder.');
            return;
        }
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
