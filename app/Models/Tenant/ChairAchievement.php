<?php

namespace App\Models\Tenant;

use App\Enums\PeriodType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChairAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'chair_id',
        'period_type',
        'year',
        'period_number',
        'target_value',
        'achieved_value',
        'achievement_percentage'
    ];

    protected $casts = [
        'period_type' => PeriodType::class,
        'target_value' => 'decimal:2',
        'achieved_value' => 'decimal:2',
        'achievement_percentage' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    /**
     * Get the chair
     */
    public function chair()
    {
        return $this->belongsTo(Chair::class);
    }

    /**
     * Scope for specific period type
     */
    public function scopeOfType($query, PeriodType $type)
    {
        return $query->where('period_type', $type);
    }

    /**
     * Scope for specific year
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope for specific period
     */
    public function scopeForPeriod($query, int $year, int $periodNumber)
    {
        return $query->where('year', $year)
            ->where('period_number', $periodNumber);
    }

    /**
     * Scope for achieved targets
     */
    public function scopeAchieved($query)
    {
        return $query->where('achievement_percentage', '>=', 100);
    }

    /**
     * Scope for not achieved targets
     */
    public function scopeNotAchieved($query)
    {
        return $query->where('achievement_percentage', '<', 100);
    }

    /**
     * Scope for monthly achievements
     */
    public function scopeMonthly($query)
    {
        return $query->where('period_type', PeriodType::MONTHLY);
    }

    /**
     * Scope for quarterly achievements
     */
    public function scopeQuarterly($query)
    {
        return $query->where('period_type', PeriodType::QUARTERLY);
    }

    /**
     * Scope for yearly achievements
     */
    public function scopeYearly($query)
    {
        return $query->where('period_type', PeriodType::YEARLY);
    }

    /**
     * Check if target was achieved
     */
    public function wasAchieved(): bool
    {
        return $this->achievement_percentage >= 100;
    }

    /**
     * Get formatted period string
     */
    public function getPeriodStringAttribute(): string
    {
        return match ($this->period_type) {
            PeriodType::MONTHLY => date('F Y', mktime(0, 0, 0, $this->period_number, 1, $this->year)),
            PeriodType::QUARTERLY => "Q{$this->period_number} {$this->year}",
            PeriodType::YEARLY => (string) $this->year,
        };
    }

    /**
     * Get difference between achieved and target
     */
    public function getDifferenceAttribute(): float
    {
        return (float) ($this->achieved_value - $this->target_value);
    }
}
