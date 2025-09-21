<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class ItemAttribute extends Model
{
    protected $table = 'item_attributes';
    protected $fillable = ['name'];
    public function values()
    {
        return $this->hasMany(ItemAttributeValue::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_attributes');
    }
}
