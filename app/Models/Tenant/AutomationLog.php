<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'automation_type',
        'entity_type',
        'entity_id',
        'status', 
        'action_taken',
        'metadata',
        'error_message',
        'triggered_by_id',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Relationships
     */
    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_id');
    }

    /**
     * Scopes
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('automation_type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForEntity($query, string $entityType, int $entityId)
    {
        return $query->where('entity_type', $entityType)
            ->where('entity_id', $entityId);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Static helper methods
     */
    public static function logSuccess(
        string $type,
        string $entityType,
        int $entityId,
        string $actionTaken,
        ?array $metadata = null,
        ?int $triggeredById = null
    ): self {
        return self::create([
            'automation_type' => $type,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'status' => 'success',
            'action_taken' => $actionTaken,
            'metadata' => $metadata,
            'triggered_by_id' => $triggeredById,
        ]);
    }

    public static function logFailure(
        string $type,
        string $entityType,
        int $entityId,
        string $errorMessage,
        ?array $metadata = null,
        ?int $triggeredById = null
    ): self {
        return self::create([
            'automation_type' => $type,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'status' => 'failed',
            'error_message' => $errorMessage,
            'metadata' => $metadata,
            'triggered_by_id' => $triggeredById,
        ]);
    }

    public static function logSkipped(
        string $type,
        string $entityType,
        int $entityId,
        string $reason,
        ?array $metadata = null
    ): self {
        return self::create([
            'automation_type' => $type,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'status' => 'skipped',
            'action_taken' => $reason,
            'metadata' => $metadata,
        ]);
    }
}

