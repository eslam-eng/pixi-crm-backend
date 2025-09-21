<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\Pipeline;
use Illuminate\Database\Seeder;

class PipelineSeeder extends Seeder
{
    public function run()
    {
        $pipeline = Pipeline::updateOrCreate([
            'name' => 'Default',
        ]);

        $pipeline->stages()->updateOrCreate([
            'name' => 'Stage 1',
            'probability' => 10,
            'seq_number' => 1,
        ]);
        $pipeline->stages()->updateOrCreate([
            'name' => 'Stage 2',
            'probability' => 20,
            'seq_number' => 2,
        ]);
        $pipeline->stages()->updateOrCreate([
            'name' => 'Stage 3',
            'probability' => 30,
            'seq_number' => 3,
        ]);
        $pipeline->stages()->updateOrCreate([
            'name' => 'Stage 4',
            'probability' => 40,
            'seq_number' => 4,
        ]);
    }
}
