<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChairAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'chair_id', 'period_type', 'year', 'period_number',
        'target_value', 'achieved_value', 'achievement_percentage'
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'achieved_value' => 'decimal:2',
        'achievement_percentage' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    public function chair()
    {
        return $this->belongsTo(Chair::class);
    }
}
