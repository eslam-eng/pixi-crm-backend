<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use Filterable;
    protected $fillable =
    [
        'name',
        'description',
        'price',
        'category_id',
        'itemable_type',
        'itemable_id',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    public function itemable()
    {
        return $this->morphTo();
    }

    // Accessor for specific attributes
    public function getIsProductAttribute()
    {
        return $this->itemable_type === 'product';
    }

    public function getIsServiceAttribute()
    {
        return $this->itemable_type === 'service';
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

    public function variants()
    {
        return $this->hasMany(ItemVariant::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'itemable_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'itemable_id');
    }
}
