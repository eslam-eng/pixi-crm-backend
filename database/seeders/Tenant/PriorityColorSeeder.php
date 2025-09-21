<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\PriorityColor;
use Illuminate\Database\Seeder;

class PriorityColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            [
                'name' => 'blue',
                'hex_code' => '#3B82F6',
            ],
            [
                'name' => 'yellow',
                'hex_code' => '#EAB308',
            ],
            [
                'name' => 'purple',
                'hex_code' => '#A855F7',
            ],
            [
                'name' => 'orange',
                'hex_code' => '#F97316',
            ],
            [
                'name' => 'green',
                'hex_code' => '#22C55E',
            ],
            [
                'name' => 'red',
                'hex_code' => '#EF4444',
            ],
            [
                'name' => 'gray',
                'hex_code' => '#6B7280',
            ],
            [
                'name' => 'indigo',
                'hex_code' => '#6366F1',
            ],
            [
                'name' => 'pink',
                'hex_code' => '#EC4899',
            ],
            [
                'name' => 'teal',
                'hex_code' => '#14B8A6',
            ],
        ];

        foreach ($colors as $color) {
            PriorityColor::create($color);
        }
    }
}
