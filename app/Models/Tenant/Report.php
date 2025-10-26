<?php

namespace App\Models\Tenant;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use Filterable;

    protected $fillable = [
        'name',
        'description',
        'report_type',
        'category',
        'is_active',
        'is_scheduled',
        'schedule_frequency',
        'schedule_time',
        'recipients',
        'created_by_id',
        'last_run_at',
        'next_run_at',
        'settings',
        'permissions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_scheduled' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'recipients' => 'array',
        'settings' => 'array',
        'permissions' => 'array',
    ];

    /**
     * Get the user who created the report
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the report executions
     */
    public function executions(): HasMany
    {
        return $this->hasMany(ReportExecution::class);
    }

    /**
     * Get the latest execution
     */
    public function latestExecution(): HasMany
    {
        return $this->hasMany(ReportExecution::class)->latest();
    }

    /**
     * Scope for active reports
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for scheduled reports
     */
    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true);
    }

    /**
     * Scope by report type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
