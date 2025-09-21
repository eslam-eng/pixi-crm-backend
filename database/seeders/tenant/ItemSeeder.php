<?php

namespace Database\Seeders\tenant;

use App\Enums\ItemType;
use App\Models\Tenant\Item;
use App\Models\Tenant\ItemCategory;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (Item::count() == 0) {

            $category_id_product = ItemCategory::where('type', ItemType::PRODUCT->value)->whereNotNull('parent_id')->first()->id;
            $category_id_service = ItemCategory::where('type', ItemType::SERVICE->value)->whereNotNull('parent_id')->first()->id;

            Item::updateOrCreate([
                'name' => 'Item 1',
                'description' => 'Item 1 description',
                'sku' => 'ITEM1',
                'price' => 100,
                'stock' => 10,
                'category_id' => $category_id_product,
                'type' => ItemType::PRODUCT->value,
            ]);
            Item::updateOrCreate([
                'name' => 'Item 2',
                'description' => 'Item 2 description',
                'sku' => 'ITEM2',
                'price' => 200,
                'stock' => 20,
                'category_id' => $category_id_service,
                'type' => ItemType::SERVICE->value,
            ]);
            Item::updateOrCreate([
                'name' => 'Item 3',
                'description' => 'Item 3 description',
                'sku' => 'ITEM3',
                'price' => 300,
                'stock' => 30,
                'category_id' => $category_id_service,
                'type' => ItemType::SERVICE->value,
            ]);
        }
    }
}
