<?php

namespace App\Models\Tenant;

use App\Enums\PeriodType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChairTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'chair_id',
        'period_type',
        'year',
        'period_number',
        'target_value',
        'effective_from',
        'effective_to'
    ];

    protected $casts = [
        'period_type' => PeriodType::class,
        'effective_from' => 'date',
        'effective_to' => 'date',
        'target_value' => 'decimal:2',
    ];

    /**
     * Get the chair
     */
    public function chair()
    {
        return $this->belongsTo(Chair::class);
    }

    /**
     * Scope to get active targets
     */
    public function scopeActive($query)
    {
        return $query->whereNull('effective_to');
    }

    /**
     * Scope to get target for a specific date
     */
    public function scopeForDate($query, $date)
    {
        $date = Carbon::parse($date);
        return $query->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $date);
            });
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
     * Scope for specific period (year + period_number)
     */
    public function scopeForPeriod($query, int $year, int $periodNumber)
    {
        return $query->where('year', $year)
            ->where('period_number', $periodNumber);
    }

    /**
     * Scope for monthly targets
     */
    public function scopeMonthly($query)
    {
        return $query->where('period_type', PeriodType::MONTHLY);
    }

    /**
     * Scope for quarterly targets
     */
    public function scopeQuarterly($query)
    {
        return $query->where('period_type', PeriodType::QUARTERLY);
    }

    /**
     * Scope for yearly targets
     */
    public function scopeYearly($query)
    {
        return $query->where('period_type', PeriodType::YEARLY);
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
     * Validate period number is valid for period type
     */
    public function isValidPeriodNumber(): bool
    {
        return $this->period_type->isValidPeriodNumber($this->period_number);
    }
}
