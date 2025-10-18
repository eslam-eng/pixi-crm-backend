<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExecution extends Model
{
    use Filterable;

    protected $fillable = [
        'report_id',
        'executed_by_id',
        'status',
        'started_at',
        'completed_at',
        'execution_time',
        'records_processed',
        'file_path',
        'file_size',
        'error_message',
        'parameters',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'execution_time' => 'integer',
        'records_processed' => 'integer',
        'file_size' => 'integer',
        'parameters' => 'array',
    ];

    /**
     * Get the report
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the user who executed the report
     */
    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by_id');
    }

    /**
     * Scope for successful executions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed executions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for running executions
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    /**
     * Get execution duration in seconds
     */
    public function getDurationAttribute(): int
    {
        if ($this->completed_at && $this->started_at) {
            return $this->completed_at->diffInSeconds($this->started_at);
        }
        return 0;
    }

    /**
     * Check if execution is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if execution failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if execution is running
     */
    public function isRunning(): bool
    {
        return $this->status === 'running';
    }
}
