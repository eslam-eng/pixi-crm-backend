<?php

namespace Database\Seeders;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Models\Central\Source;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Source::count() > 0) {
            $this->command->info('Sources already exist. Skipping SourcesTableSeeder.');
            return;
        }
        $sources = [
            [
                'name' => 'AppSumo',
                'is_active' => ActivationStatusEnum::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Envato',
                'is_active' => ActivationStatusEnum::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];

        DB::connection('landlord')
            ->table('sources')->insert($sources);
    }
}
