<?php

namespace App\Models\Tenant;

use App\Models\TaskReminder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Reminder extends Model
{
    use  HasTranslations;

    protected $fillable = [
        'name',
        'time_value',
        'time_unit',
        'is_default',
        'sort_order'
    ];

    public $translatable = ['name'];

    protected $casts = [
        'is_default' => 'boolean',
        'time_value' => 'integer',
        'sort_order' => 'integer',
    ];

    public function getLocalizedNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }
    /**
     * Scope to get reminders ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Scope to get default reminder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get the total minutes for cron job scheduling
     */
    public function getTotalMinutesAttribute(): int
    {
        if ($this->time_unit === 'on_time') {
            return 0;
        }

        $multipliers = [
            'minutes' => 1,
            'hours' => 60,
            'days' => 1440, // 24 * 60
            'weeks' => 10080, // 7 * 24 * 60
        ];

        return $this->time_value * ($multipliers[$this->time_unit] ?? 0);
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->getTranslation('name', app()->getLocale());

        if ($this->time_unit === 'on_time') {
            return $name;
        }

        return $name . ' ' . $this->time_value . ' ' . $this->time_unit;
    }

    /**
     * Get the tasks that use this reminder.
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_reminders')
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
}
