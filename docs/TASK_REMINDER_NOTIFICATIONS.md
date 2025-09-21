# Task Reminder Notifications

This document explains the task reminder notification system that sends emails and push notifications to users when task reminders are due.

## Overview

The system automatically sends notifications to users assigned to tasks when reminders are triggered. It supports both email notifications and in-app/push notifications via FCM (Firebase Cloud Messaging).

## Features

- **Email Notifications**: HTML and text email templates for task reminders
- **In-App Notifications**: Database-stored notifications for the application
- **Push Notifications**: FCM push notifications for mobile devices
- **Multiple Reminder Types**: Support for various reminder timings (1 hour before, 1 day before, on time, etc.)
- **Automatic Processing**: Cron job processes reminders automatically

## Components

### 1. Email System

#### TaskReminderMail
- **Location**: `app/Mail/TaskReminderMail.php`
- **Templates**: 
  - `resources/views/emails/task-reminder.blade.php` (HTML)
  - `resources/views/emails/task-reminder-text.blade.php` (Text)

#### Features
- Responsive HTML email design
- Priority-based styling
- Task details and due date information
- Action button to view task

### 2. Notification System

#### TaskReminderNotification
- **Location**: `app/Notifications/TaskReminderNotification.php`
- **Channels**: Database, FCM, Mail
- **Features**:
  - Database notifications for in-app display
  - FCM push notifications for mobile devices
  - Automatic FCM token management

### 3. FCM Token Management

#### FcmToken Model
- **Location**: `app/Models/FcmToken.php`
- **Table**: `fcm_tokens`
- **Features**:
  - Device type tracking (android, ios, web)
  - Active/inactive token management
  - Last used timestamp

#### API Endpoints
- `GET /api/fcm-tokens` - Get user's FCM tokens
- `POST /api/fcm-tokens` - Register new FCM token
- `DELETE /api/fcm-tokens` - Remove FCM token

### 4. Reminder Processing

#### ReminderService
- **Location**: `app/Services/Tenant/ReminderService.php`
- **Features**:
  - Automatic reminder detection
  - Email and notification sending
  - Reminder status tracking
  - Error handling and logging

#### ProcessTaskReminders Command
- **Command**: `php artisan reminders:process --tenant={tenant_id}`
- **Purpose**: Process pending reminders for a specific tenant
- **Usage**: Can be run via cron job

## Database Schema

### FCM Tokens Table
```sql
CREATE TABLE fcm_tokens (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    device_type VARCHAR(50) NULL,
    device_id VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_used_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_active (user_id, is_active)
);
```

### Task Reminders Table
```sql
CREATE TABLE task_reminders (
    id BIGINT PRIMARY KEY,
    task_id BIGINT NOT NULL,
    reminder_id BIGINT NOT NULL,
    reminder_at TIMESTAMP NULL,
    is_sent BOOLEAN DEFAULT FALSE,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (reminder_id) REFERENCES reminders(id) ON DELETE CASCADE,
    UNIQUE KEY unique_task_reminder (task_id, reminder_id)
);
```

## Usage

### 1. Setting Up Reminders

```php
use App\Models\Tenant\Task;
use App\Models\Tenant\Reminder;

// Get a task
$task = Task::find(1);

// Get available reminders
$oneDayBefore = Reminder::where('time_value', 1)->where('time_unit', 'days')->first();
$oneHourBefore = Reminder::where('time_value', 1)->where('time_unit', 'hours')->first();
$onTime = Reminder::where('time_unit', 'on_time')->first();

// Add reminders to the task
$task->addReminder($oneDayBefore);
$task->addReminder($oneHourBefore);
$task->addReminder($onTime);
```

### 2. Processing Reminders

#### Manual Processing
```bash
# Process reminders for a specific tenant
php artisan reminders:process --tenant=1

# Process reminders for all tenants
php artisan reminders:process
```

#### Cron Job Setup
Add to your crontab:
```bash
# Run every 15 minutes
*/15 * * * * cd /path/to/your/project && php artisan reminders:process >> /dev/null 2>&1
```

### 3. FCM Token Registration

#### Frontend (JavaScript)
```javascript
// Register FCM token
const registerFCMToken = async (token, deviceType = 'web') => {
    try {
        const response = await fetch('/api/fcm-tokens', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${userToken}`
            },
            body: JSON.stringify({
                token: token,
                device_type: deviceType,
                device_id: getDeviceId()
            })
        });
        
        const data = await response.json();
        console.log('FCM token registered:', data);
    } catch (error) {
        console.error('Failed to register FCM token:', error);
    }
};
```

#### Mobile (React Native)
```javascript
import messaging from '@react-native-firebase/messaging';

// Register FCM token
const registerFCMToken = async () => {
    try {
        const token = await messaging().getToken();
        await fetch('/api/fcm-tokens', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${userToken}`
            },
            body: JSON.stringify({
                token: token,
                device_type: Platform.OS,
                device_id: await getUniqueId()
            })
        });
    } catch (error) {
        console.error('Failed to register FCM token:', error);
    }
};
```

## Configuration

### Email Configuration
Update your `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your App Name"
```

### FCM Configuration
Update your `.env` file:
```env
FCM_SERVER_KEY=your-fcm-server-key
FCM_SENDER_ID=your-fcm-sender-id
```

## Testing

### Test Command
```bash
# Test reminders for a specific tenant
php artisan test:task-reminders --tenant=1
```

### Manual Testing
1. Create a task with a due date in the near future
2. Add a reminder to the task
3. Run the reminder processing command
4. Check logs for notification sending status

## Troubleshooting

### Common Issues

1. **Reminders not being sent**
   - Check if the task has an assigned user
   - Verify the reminder timing calculation
   - Check the application logs

2. **Email not being sent**
   - Verify email configuration
   - Check mail queue status
   - Verify user email address

3. **FCM notifications not working**
   - Verify FCM token registration
   - Check FCM server key configuration
   - Verify device token validity

### Logs
Check the following log files:
- `storage/logs/laravel.log` - General application logs
- `storage/logs/reminders.log` - Reminder-specific logs (if configured)

## Security Considerations

1. **FCM Token Security**
   - Tokens are stored securely in the database
   - Inactive tokens are automatically cleaned up
   - Device information is tracked for security

2. **Email Security**
   - Email addresses are validated before sending
   - Sensitive information is not included in emails
   - Rate limiting can be implemented

3. **Notification Privacy**
   - Notifications are only sent to assigned users
   - User preferences can be implemented for notification types
   - GDPR compliance considerations for data retention