<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class ItemVariant extends Model
{
    use Filterable;

    protected $fillable = ['product_id', 'sku', 'price', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(ItemAttributeValue::class, 'item_variants_attribute_values', 'variant_id', 'item_attribute_value_id')
            ->withPivot('item_attribute_id');
    }

    public function getAttributesArray()
    {
        return $this->attributeValues()
            ->with('attribute')
            ->get()
            ->mapWithKeys(function ($attributeValue) {
                return [$attributeValue->attribute->name => $attributeValue->value];
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy("created_at");
    }
}
