# Task Reminders Usage Guide

## Overview

The task reminders system now supports multiple reminders per task through a many-to-many relationship. This allows you to set up various reminder types for a single task (e.g., 1 day before, 1 hour before, and on time).

## Database Structure

### Tables
- `tasks` - Main tasks table (removed `reminder_id` foreign key)
- `reminders` - Available reminder types (e.g., "1 day before", "1 hour before")
- `task_reminders` - Pivot table linking tasks to reminders with additional metadata

### Pivot Table Fields
- `task_id` - Foreign key to tasks table
- `reminder_id` - Foreign key to reminders table
- `reminder_at` - Calculated timestamp when reminder should be triggered
- `is_sent` - Boolean flag indicating if reminder has been sent
- `sent_at` - Timestamp when reminder was actually sent

## Usage Examples

### Adding Reminders to a Task

```php
use App\Models\Tenant\Task;
use App\Models\Tenant\Reminder;

// Get a task
$task = Task::find(1);

// Get available reminders
$oneDayBefore = Reminder::where('time_value', 1)->where('time_unit', 'days')->first();
$oneHourBefore = Reminder::where('time_value', 1)->where('time_unit', 'hours')->first();
$onTime = Reminder::where('time_unit', 'on_time')->first();

// Add multiple reminders to the task
$task->addReminder($oneDayBefore);
$task->addReminder($oneHourBefore);
$task->addReminder($onTime);
```

### Retrieving Task Reminders

```php
// Get all reminders for a task
$task = Task::with('reminders')->find(1);
$reminders = $task->reminders;

// Get reminders with pivot data
$taskReminders = $task->taskReminders;
foreach ($taskReminders as $taskReminder) {
    echo "Reminder: " . $taskReminder->reminder->display_name;
    echo "Scheduled for: " . $taskReminder->reminder_at;
    echo "Sent: " . ($taskReminder->is_sent ? 'Yes' : 'No');
}
```

### Retrieving Tasks for a Reminder

```php
// Get all tasks using a specific reminder
$reminder = Reminder::find(1);
$tasks = $reminder->tasks;

// Get tasks with pivot data
$taskReminders = $reminder->taskReminders;
foreach ($taskReminders as $taskReminder) {
    echo "Task: " . $taskReminder->task->task_title;
    echo "Reminder scheduled for: " . $taskReminder->reminder_at;
}
```

### Managing Reminder Status

```php
// Mark a reminder as sent
$taskReminder = TaskReminder::where('task_id', 1)->where('reminder_id', 1)->first();
$taskReminder->update([
    'is_sent' => true,
    'sent_at' => now()
]);

// Get unsent reminders
$unsentReminders = TaskReminder::where('is_sent', false)
    ->where('reminder_at', '<=', now())
    ->get();
```

### Removing Reminders

```php
// Remove a specific reminder from a task
$task = Task::find(1);
$reminder = Reminder::find(1);
$task->reminders()->detach($reminder->id);

// Remove all reminders from a task
$task->reminders()->detach();
```

## Migration Notes

1. The `reminder_id` foreign key has been removed from the `tasks` table
2. A new `task_reminders` pivot table has been created
3. Existing data migration may be needed if you have tasks with existing reminder relationships

## Benefits

- **Flexibility**: Multiple reminder types per task
- **Tracking**: Track which reminders have been sent and when
- **Scalability**: Easy to add new reminder types without schema changes
- **Querying**: Efficient queries for reminder processing and reporting