# Reminder System Setup

## Overview
The reminder system allows you to set up automated reminders for tasks based on configurable time intervals. The system supports multiple languages and provides both API endpoints and cron job processing.

## Database Structure

### Reminders Table
- `id`: Primary key
- `name`: JSON field storing translations (English and Arabic)
- `time_value`: Numeric value for the time interval
- `time_unit`: Unit of time (minutes, hours, days, weeks, on_time)
- `is_default`: Boolean flag for default reminder option
- `sort_order`: Integer for ordering in dropdowns
- `created_at`, `updated_at`: Timestamps

## API Endpoints

### Get All Reminders
```
GET /api/reminders
```

### Get Default Reminder
```
GET /api/reminders-default
```

### Set Default Reminder
```
PATCH /api/reminders/{id}/set-default
```

### Create Reminder
```
POST /api/reminders
{
    "name": {
        "en": "Custom reminder",
        "ar": "تذكير مخصص"
    },
    "time_value": 45,
    "time_unit": "minutes",
    "is_default": false,
    "sort_order": 10
}
```

## Cron Job Setup

### 1. Add to Laravel Scheduler
Add this to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Process reminders every 5 minutes
    $schedule->command('reminders:process')->everyFiveMinutes();
    
    // Or process for specific tenant
    $schedule->command('reminders:process --tenant=tenant-id')->everyFiveMinutes();
}
```

### 2. System Cron Job
Add this to your server's crontab:

```bash
# Process reminders every 5 minutes
*/5 * * * * cd /path/to/your/project && php artisan reminders:process >> /dev/null 2>&1
```

### 3. Tenant-specific Processing
For multi-tenant applications, you can process reminders for specific tenants:

```bash
# Process reminders for specific tenant
*/5 * * * * cd /path/to/your/project && php artisan reminders:process --tenant=tenant-id >> /dev/null 2>&1
```

## Usage Examples

### In Controllers
```php
use App\Services\Tenant\Tasks\ReminderService;

public function getReminders()
{
    $reminderService = new ReminderService();
    $reminders = $reminderService->getAllReminders();
    
    return response()->json($reminders);
}
```

### In Task Scheduling
```php
use App\Services\Tenant\ReminderService;
use Carbon\Carbon;

public function scheduleTaskReminder($task, $reminderId)
{
    $reminderService = new ReminderService();
    $reminder = $reminderService->getReminderById($reminderId);
    
    if ($reminder) {
        $reminderTime = $reminderService->calculateReminderTime(
            $task->due_date, 
            $reminder
        );
        
        // Schedule the reminder
        // Implementation depends on your notification system
    }
}
```

## Default Reminder Options

The system comes pre-configured with these reminder options:

1. **On time** (0 minutes)
2. **5 minutes before** (5 minutes)
3. **15 minutes before** (15 minutes)
4. **30 minutes before** (30 minutes) - *Default selection*
5. **1 hour before** (60 minutes)
6. **2 hours before** (120 minutes)
7. **1 day before** (1440 minutes)
8. **2 days before** (2880 minutes)
9. **1 week before** (10080 minutes)

## Translation Support

The system supports both English and Arabic translations. To add more languages:

1. Update the `config/translatable.php` file
2. Add translations to the seeder
3. Update the middleware to support new locales

## Customization

### Adding New Reminder Types
1. Add new entries to the seeder
2. Update the validation rules in the controller
3. Add any new time units to the enum in the migration

### Custom Notification Methods
Override the `sendReminderNotification` method in `ReminderService` to implement your preferred notification system (email, SMS, push notifications, etc.).

## Monitoring

Monitor reminder processing through:
- Laravel logs
- Database queries
- Custom monitoring endpoints

The system logs all reminder processing activities for debugging and monitoring purposes.