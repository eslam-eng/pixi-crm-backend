# Escalation Sent Field Usage Guide

## Overview

The `escalation_sent` field has been added to the tasks table to track whether escalation notifications have been sent for overdue tasks. This prevents duplicate escalation notifications from being sent for the same task.

## Database Changes

### Migration
- **File**: `database/migrations/tenant/2025_09_14_010956_add_escalation_sent_to_tasks_table.php`
- **Column**: `escalation_sent` (boolean, default: false)
- **Position**: Added after `additional_notes` column

### Schema
```sql
ALTER TABLE tasks ADD COLUMN escalation_sent TINYINT(1) NOT NULL DEFAULT 0 AFTER additional_notes;
```

## Model Updates

### Task Model (`app/Models/Tenant/Task.php`)
- Added `escalation_sent` to `$fillable` array
- Added `escalation_sent` to `$casts` array as boolean

### TaskDTO (`app/DTO/Tenant/TaskDTO.php`)
- Added `escalation_sent` parameter to constructor
- Added `escalation_sent` to `fromRequest()` method
- Added `escalation_sent` to `toArray()` method
- Added `escalation_sent` to `fromArray()` method

### TaskResource (`app/Http/Resources/Tenant/Tasks/TaskResource.php`)
- Added `escalation_sent` field to API response

## Escalation Command Updates

### ProcessTaskEscalations Command (`app/Console/Commands/ProcessTaskEscalations.php`)

#### Query Filtering
- Added `->where('escalation_sent', false)` to only process tasks that haven't had escalation sent yet
- This prevents duplicate escalation notifications

#### Escalation Tracking
- After successfully sending notifications, the command sets `escalation_sent = true`
- Tracks notification success/failure counts
- Only marks as sent if at least one notification was sent successfully
- Even if no users to notify, still marks as escalation sent to prevent reprocessing

#### Logic Flow
1. Find overdue tasks where `escalation_sent = false`
2. Process each task for escalation
3. Send notifications to followers and assigned users
4. If at least one notification sent successfully, set `escalation_sent = true`
5. Log success/failure counts

## API Response

The `escalation_sent` field is now included in task API responses:

```json
{
    "id": 123,
    "title": "Complete Project Review",
    "description": "Review the project documentation",
    "status": "pending",
    "escalation_sent": false,
    "due_date": "2024-12-15",
    "due_time": "14:00",
    "priority": {
        "id": 1,
        "name": "High",
        "level": 3,
        "hex_code": "#ff0000"
    },
    "assigned_to": {
        "id": 5,
        "name": "John Doe",
        "role": "Manager"
    }
}
```

## Usage Examples

### Check if Task Has Escalation Sent
```php
$task = Task::find(1);
if ($task->escalation_sent) {
    echo "Escalation notifications have been sent for this task";
} else {
    echo "No escalation notifications sent yet";
}
```

### Reset Escalation Status (if needed)
```php
$task = Task::find(1);
$task->update(['escalation_sent' => false]);
```

### Query Tasks Without Escalation
```php
$tasksWithoutEscalation = Task::where('escalation_sent', false)
    ->where('status', '!=', 'completed')
    ->where('due_date', '<', now())
    ->get();
```

### Query Tasks With Escalation Sent
```php
$tasksWithEscalation = Task::where('escalation_sent', true)
    ->where('status', '!=', 'completed')
    ->where('due_date', '<', now())
    ->get();
```

## Benefits

1. **Prevents Duplicate Notifications**: Tasks won't receive multiple escalation notifications
2. **Performance**: Reduces unnecessary processing of already escalated tasks
3. **Tracking**: Easy to see which tasks have had escalation notifications sent
4. **Debugging**: Helps identify issues with escalation system
5. **Reporting**: Can generate reports on escalation patterns

## Migration Instructions

1. **Run Migration**: The migration will be automatically applied to tenant databases
2. **Existing Tasks**: All existing tasks will have `escalation_sent = false` by default
3. **Cron Job**: The escalation command will now only process tasks with `escalation_sent = false`

## Testing

### Manual Testing
```bash
# Run escalation command for specific tenant
php artisan tasks:escalate --tenant=tenant_id

# Run escalation command for all tenants
php artisan tasks:escalate
```

### Expected Behavior
1. First run: Processes all overdue tasks, sends notifications, sets `escalation_sent = true`
2. Subsequent runs: Skips tasks with `escalation_sent = true`
3. New overdue tasks: Will be processed in next run

## Troubleshooting

### Reset Escalation Status
If you need to resend escalation notifications for a task:
```php
$task = Task::find($taskId);
$task->update(['escalation_sent' => false]);
```

### Check Escalation Status
```php
$task = Task::find($taskId);
echo $task->escalation_sent ? 'Escalation sent' : 'No escalation sent';
```

### Bulk Reset (if needed)
```php
Task::where('escalation_sent', true)->update(['escalation_sent' => false]);
```

## Security Considerations

- The `escalation_sent` field is not included in form validation (TaskRequest)
- It should only be modified by the system (cron job), not by user input
- API responses include the field for transparency but it's read-only for users
