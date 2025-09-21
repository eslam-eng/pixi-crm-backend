<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\ItemAttribute;
use App\Models\Tenant\ItemAttributeValue;
use Illuminate\Database\Seeder;

class AttributeWithValueProductSeeder extends Seeder
{
    public function run(): void
    {

        if (ItemAttribute::count() == 0 && ItemAttributeValue::count() == 0) {
            ItemAttribute::upsert(
                [
                    ['name' => 'color'],
                    ['name' => 'size'],
                ],
                ['name'],
                []
            );

            ItemAttributeValue::upsert(
                [
                    ['value' => 'red', 'item_attribute_id' => 1],
                    ['value' => 'blue', 'item_attribute_id' => 1],
                    ['value' => 'small', 'item_attribute_id' => 2],
                    ['value' => 'medium', 'item_attribute_id' => 2],
                ],
                ['value', 'item_attribute_id'],
                []
            );
        }
    }
}
