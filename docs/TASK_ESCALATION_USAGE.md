# Task Escalation System Usage Guide

## Overview

The task escalation system automatically sends notifications to task followers and assigned users when tasks become overdue based on the configured `escalation_time_hours` setting. This ensures that overdue tasks receive proper attention and follow-up.

## Configuration

### Settings

The escalation system is controlled by the `TasksSettings` class with the following configuration:

- `enable_escalation` (boolean): Whether escalation notifications are enabled (default: true)
- `escalation_time_hours` (integer): Hours to wait after due date before sending escalation notifications (default: 24)

### Database Settings

Settings are stored in the `settings` table and can be managed through the `TasksSettings` class:

```php
use App\Settings\TasksSettings;

$tasksSettings = new TasksSettings();
$tasksSettings->enable_escalation = true;
$tasksSettings->escalation_time_hours = 48; // 48 hours
$tasksSettings->save();
```

## How It Works

### 1. Task Escalation Logic

The system identifies overdue tasks by:
- Finding tasks with status other than 'completed' or 'cancelled'
- Checking if the current time exceeds the due date/time by more than `escalation_time_hours`
- Considering both date and time components for precise calculation

### 2. Notification Recipients

For each overdue task, notifications are sent to:
- The assigned user (if any)
- All task followers
- Duplicates are automatically removed

### 3. Notification Channels

Escalation notifications are sent via:
- **Email**: Detailed task information with due date, hours overdue, priority, etc.
- **Database**: Stored in the `notifications` table for in-app display

## Command Usage

### Manual Execution

```bash
# Process escalations for all tenants
php artisan tasks:escalate

# Process escalations for a specific tenant
php artisan tasks:escalate --tenant=tenant_id
```

### Automated Scheduling

The escalation command is automatically scheduled to run every hour:

```php
// In routes/console.php
Schedule::command('tasks:escalate')->hourly()->withoutOverlapping();
```

## Notification Content

### Email Notifications

Email notifications include:
- Task title and description
- Due date and time
- Hours overdue
- Priority level
- Assigned user
- Direct link to view the task

### Database Notifications

Database notifications contain:
- Task ID and title
- Detailed task information
- Hours overdue calculation
- Priority and assignment details
- Action URL for task viewing
- Notification type identifier

## Database Structure

### Required Tables

- `tasks` - Main tasks table
- `tasks_followers` - Task followers relationship
- `users` - User information
- `notifications` - Notification storage
- `settings` - System settings storage

### Task Status Requirements

Tasks must have status other than:
- `completed`
- `cancelled`

## Monitoring and Logging

### Command Output

The command provides detailed output including:
- Number of tenants processed
- Number of tasks found for escalation
- Individual task processing status
- User notification results

### Error Handling

- Individual task processing errors don't stop the entire process
- Failed notifications are logged with detailed error information
- Tenant-specific errors are isolated and logged separately

### Logging

All errors and important events are logged to the Laravel log system with context including:
- Tenant ID
- Task ID
- User ID (for notification failures)
- Error messages and stack traces

## Best Practices

### 1. Escalation Timing

- Set appropriate `escalation_time_hours` based on your business needs
- Consider different escalation times for different task types or priorities
- Monitor escalation frequency to avoid notification fatigue

### 2. User Management

- Ensure task followers are properly assigned
- Keep user information up to date
- Consider notification preferences for different users

### 3. Monitoring

- Regularly check logs for processing errors
- Monitor notification delivery success rates
- Review escalation patterns to optimize task management

## Troubleshooting

### Common Issues

1. **No escalations being sent**
   - Check if `enable_escalation` is set to true
   - Verify `escalation_time_hours` is not too high
   - Ensure tasks have followers or assigned users

2. **Missing notifications**
   - Check user email addresses are valid
   - Verify notification channels are properly configured
   - Check logs for specific error messages

3. **Performance issues**
   - Consider running escalation for specific tenants during off-peak hours
   - Monitor database performance with large numbers of tasks
   - Use the `--tenant` option to process tenants individually

### Debug Commands

```bash
# Test escalation for a specific tenant
php artisan tasks:escalate --tenant=test_tenant

# Check task settings
php artisan tinker
>>> $settings = new App\Settings\TasksSettings();
>>> $settings->enable_escalation
>>> $settings->escalation_time_hours
```

## Integration

The escalation system integrates with:
- Laravel's notification system
- Multi-tenant architecture
- Existing task management system
- Email and database notification channels
- Laravel's scheduling system

## Security Considerations

- Notifications are sent only to authorized users (followers and assigned users)
- Tenant isolation is maintained throughout the process
- Sensitive task information is only sent to relevant parties
- Error messages don't expose sensitive system information
