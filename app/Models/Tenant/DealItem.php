<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DealItem extends Pivot
{
    protected $fillable = [
        'deal_id',
        'item_id',
        'quantity',
        'price',
        'total',
    ];


    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
