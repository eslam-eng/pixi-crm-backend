<?php

namespace App\Models;

use App\Models\Tenant\Reminder;
use App\Models\Tenant\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'reminder_id',
        'reminder_at',
        'is_sent',
        'sent_at',
    ];

    protected $casts = [
        'reminder_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    /**
     * Get the task that owns the reminder.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the reminder that belongs to the task.
     */
    public function reminder(): BelongsTo
    {
        return $this->belongsTo(Reminder::class);
    }
}