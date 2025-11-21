<?php

namespace App\Models\Tenant;

use App\Models\TaskReminder;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use Filterable, LogsActivity;
    protected $fillable = [
        'title',
        'description',
        'task_type_id',
        'status',
        'priority_id',
        'due_date',
        'due_time',
        'assigned_to_id',
        'lead_id',
        'tags',
        'additional_notes',
        'escalation_sent'
    ];

    protected $casts = [
        'tags' => 'array',
        'escalation_sent' => 'boolean',
        'due_date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'tasks_followers', 'task_id', 'follower_id')
            ->withTimestamps();
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type_id', 'id');
    }

    /**
     * Get the priority for the task.
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    /**
     * Get the assigned user for the task.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    /**
     * Get the reminders for the task.
     */
    public function reminders()
    {
        return $this->belongsToMany(Reminder::class, 'task_reminders')
            ->withPivot(['reminder_at', 'is_sent', 'sent_at'])
            ->withTimestamps();
    }

    /**
     * Get the task reminders pivot records.
     */
    public function taskReminders()
    {
        return $this->hasMany(TaskReminder::class);
    }

    /**
     * Add a reminder to the task.
     */
    public function addReminder(Reminder $reminder, $reminderAt = null)
    {
        $reminderAt = $reminderAt ?? $this->calculateReminderTime($reminder);

        $this->reminders()->attach($reminder->id, [
            'reminder_at' => $reminderAt,
            'is_sent' => false,
        ]);
    }

    /**
     * Calculate reminder time based on task due date and reminder settings.
     */
    private function calculateReminderTime(Reminder $reminder)
    {
        if ($reminder->time_unit === 'on_time') {
            $date = $this->due_date instanceof Carbon
                ? $this->due_date->copy()
                : Carbon::parse($this->due_date);
            return (clone $date)->setTimeFromTimeString((string) $this->due_time);
        }

        $date = $this->due_date instanceof Carbon
            ? $this->due_date->copy()
            : Carbon::parse($this->due_date);
        $dueDateTime = (clone $date)->setTimeFromTimeString((string) $this->due_time);
        switch ($reminder->time_unit) {
            case 'minutes':
                return $dueDateTime->subMinutes($reminder->time_value);
            case 'hours':
                return $dueDateTime->subHours($reminder->time_value);
            case 'days':
                return $dueDateTime->subDays($reminder->time_value);
            case 'weeks':
                return $dueDateTime->subWeeks($reminder->time_value);
            default:
                return $dueDateTime;
        }
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    protected static $logAttributes = [
        'title',
        'description',
        'task_type_id',
        'status',
        'priority_id',
        'due_date',
        'due_time',
        'assigned_to_id',
        'lead_id',
        'tags',
        'additional_notes',
        'escalation_sent'
    ];

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(self::$logAttributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('task')
            ->setDescriptionForEvent(fn(string $eventName) => "Task has been {$eventName}");
    }
}
