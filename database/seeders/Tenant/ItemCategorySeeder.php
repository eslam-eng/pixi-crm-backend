<?php

namespace Database\Seeders\Tenant;

use App\Enums\ItemType;
use App\Models\Tenant\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productCategory = ItemCategory::updateOrCreate([
            'name' => 'Electronics',
            'type' => ItemType::PRODUCT->value,
            'parent_id' => null,
        ]);
        ItemCategory::updateOrCreate([
            'name' => 'Electronics2',
            'type' => ItemType::PRODUCT->value,
            'parent_id' => null,
        ]);
        $serviceCategory = ItemCategory::updateOrCreate([
            'name' => 'Software',
            'type' => ItemType::SERVICE->value,
            'parent_id' => null,
        ]);
        ItemCategory::updateOrCreate([
            'name' => 'Software2',
            'type' => ItemType::SERVICE->value,
            'parent_id' => null,
        ]);

        $productCategory->children()->updateOrCreate([
            'name' => 'Electronics3',
            'type' => ItemType::PRODUCT->value,
            'parent_id' => $productCategory->id,
        ]);
        $serviceCategory->children()->updateOrCreate([
            'name' => 'Software3',
            'type' => ItemType::SERVICE->value,
            'parent_id' => $serviceCategory->id,
        ]);
    }
}
