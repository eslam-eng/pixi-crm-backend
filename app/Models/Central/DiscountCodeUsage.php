<?php

namespace App\Models\Central;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountCodeUsage extends Model
{
    use Filterable, HasFactory;

    protected $fillable = [
        'discount_code_id',
        'tenant_id',
        'subscription_id',
        'invoice_id',
    ];

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }
}
