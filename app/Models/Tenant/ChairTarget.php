<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChairTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'chair_id', 'period_type', 'year', 'period_number',
        'target_value', 'effective_from', 'effective_to'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'target_value' => 'decimal:2',
    ];

    public function chair()
    {
        return $this->belongsTo(Chair::class);
    }
}
