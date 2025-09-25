<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DealVariant extends Pivot
{
    protected $fillable = [
        'deal_id',
        'variant_id',
        'quantity',
        'price',
        'total',
    ];


    public function variant()
    {
        return $this->belongsTo(ItemVariant::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
