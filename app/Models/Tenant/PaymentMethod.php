<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;


class PaymentMethod extends Model
{
    protected $fillable = ['name', 'is_checked', 'is_default', 'is_manual_added'];

    protected $hidden = ['created_at', 'updated_at'];

    public function deals()
    {
        return $this->hasMany(Deal::class, 'payment_method_id');
    }

    /**
     * Check if the payment method can be deleted
     * 
     * @return bool
     */
    public function canDelete(): bool
    {
        // System payment methods (IDs 1, 2, 3, 4) cannot be deleted
        if (in_array($this->id, [1, 2, 3, 4])) {
            return false;
        }

        // Payment methods with associated deals cannot be deleted
        return $this->deals()->count() === 0;
    }
}
