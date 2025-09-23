<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Filterable;
    protected $fillable =
    [
        'sku',
        'stock',
        'category_id',
        'has_variants',
    ];

    protected $casts = [
        'has_variants' => 'boolean',
    ];

    // Polymorphic relationship (reverse)
    public function item(): MorphOne
    {
        return $this->morphOne(Item::class, 'itemable');
    }

    // Product variants
    public function variants()
    {
        return $this->hasMany(ItemVariant::class);
    }

    // Helper methods
    public function hasVariants()
    {
        return $this->has_variants && $this->variants()->count() > 0;
    }


    public function deals()
    {
        return $this->belongsToMany(Deal::class, 'deal_items', 'item_id', 'deal_id');
    }

    public function opportunities()
    {
        return $this->belongsToMany(Lead::class, 'leads_items', 'item_id', 'lead_id');
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    // public function variants()
    // {
    //     return $this->hasMany(ItemVariant::class);
    // }
}
