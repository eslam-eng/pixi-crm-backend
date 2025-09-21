<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ItemAttributeValue extends Model
{
    protected $table = 'item_attribute_values';
    protected $fillable = ['item_attribute_id', 'value'];

    public function attribute()
    {
        return $this->belongsTo(ItemAttribute::class, 'item_attribute_id');
    }

    public function variants()
    {
        return $this->belongsToMany(ItemVariant::class, 'item_variants_attribute_values', 'item_attribute_value_id', 'variant_id');
    }
}
