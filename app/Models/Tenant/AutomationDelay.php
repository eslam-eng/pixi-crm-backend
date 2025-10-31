<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AutomationDelay extends Model
{
    protected $fillable = [
        'automation_steps_implement_id',
        'duration',
        'unit',
        'execute_at',
        'processed',
        'processed_at',
        'context_data',
    ];

    protected $casts = [
        'processed' => 'boolean',
        'execute_at' => 'datetime',
        'processed_at' => 'datetime',
        'context_data' => 'array',
    ];

    /**
     * Get the step implementation that owns this delay
     */
    public function automationStepsImplement(): BelongsTo
    {
        return $this->belongsTo(AutomationStepsImplement::class);
    }

    /**
     * Scope for processed delays
     */
    public function scopeProcessed($query)
    {
        return $query->where('processed', true);
    }

    /**
     * Scope for pending delays
     */
    public function scopePending($query)
    {
        return $query->where('processed', false);
    }

    /**
     * Scope for delays ready to execute
     */
    public function scopeReadyToExecute($query)
    {
        return $query->where('processed', false)
                    ->where('execute_at', '<=', now());
    }

    /**
     * Scope for delays due in the next X minutes
     */
    public function scopeDueInMinutes($query, $minutes = 5)
    {
        return $query->where('processed', false)
                    ->where('execute_at', '<=', now()->addMinutes($minutes));
    }

    /**
     * Mark this delay as processed
     */
    public function markAsProcessed(): bool
    {
        return $this->update([
            'processed' => true,
            'processed_at' => now(),
        ]);
    }

    /**
     * Calculate execute_at timestamp based on duration and unit
     */
    public static function calculateExecuteAt(int $duration, string $unit): Carbon
    {
        $now = now();
        
        switch($unit) {
            case 'minutes':
                return $now->addMinutes($duration);
            case 'hours':
                return $now->addHours($duration);
            case 'days':
                return $now->addDays($duration);
            default:
                return $now->addMinutes($duration);
        }
    }

    /**
     * Get the duration in minutes
     */
    public function getDurationInMinutes(): int
    {
        switch($this->unit) {
            case 'minutes':
                return $this->duration;
            case 'hours':
                return $this->duration * 60;
            case 'days':
                return $this->duration * 24 * 60;
            default:
                return $this->duration;
        }
    }

    /**
     * Check if this delay is ready to execute
     */
    public function isReadyToExecute(): bool
    {
        return !$this->processed && $this->execute_at <= now();
    }

    /**
     * Get all delays that are ready to execute
     */
    public static function getReadyToExecute()
    {
        return self::readyToExecute()->with('automationStepsImplement')->get();
    }
}
