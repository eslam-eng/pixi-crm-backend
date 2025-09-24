<?php

namespace Database\Seeders\Tenant;

use App\Enums\ItemType;
use App\Enums\ServiceDuration;
use App\Enums\ServiceType;
use App\Models\Tenant\Item;
use App\Models\Tenant\ItemCategory;
use App\Models\Tenant\Product;
use App\Models\Tenant\Service;
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

            // 'name',
            // 'description',
            // 'price',
            // 'category_id',
            // 'itemable_type',
            // 'itemable_id',


            $product = Product::create([
                'sku' => 'product1',
                'stock' => 100,
            ]);
            $item = $product->item()->create([
                'name' => 'Item 1',
                'description' => 'Item 1 description',
                'price' => 100,
                'category_id' => $category_id_product,
            ]);

            $service = Service::create([
                'duration' => ServiceDuration::HOURLY->value,
                'service_type' => ServiceType::ONE_TIME->value,
            ]);
            $product->item()->create([
                'name' => 'Service 1',
                'description' => 'Item 1 description',
                'price' => 100,
                'category_id' => $category_id_service,
            ]);
        }
    }
}
