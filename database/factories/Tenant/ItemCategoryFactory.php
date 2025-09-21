<?php

namespace Database\Factories\Tenant;

use App\Enums\ItemType;
use App\Models\Tenant\ItemCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\ItemStatus>
 */
class ItemCategoryFactory extends Factory
{
    protected $model = ItemCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(ItemType::values());
        
        $parentId = ItemCategory::where(['type' => $type, 'parent_id' => null])->inRandomOrder()->value('id');

        do {
            $name = fake()->unique()->word();
        } while (ItemCategory::where('name', $name)->exists());

        return [
            'name' => $name,
            'type' => $type,
            'parent_id' => $parentId,
        ];
    }
}
