# Notification API Usage Guide

## Overview

The Notification API provides comprehensive endpoints for managing user notifications in the system. It supports retrieving, marking as read, deleting, and getting statistics for notifications.

## Authentication

All notification endpoints require authentication using Laravel Sanctum. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## API Endpoints

### 1. Get Notifications

**GET** `/api/notifications`

Retrieve paginated notifications for the authenticated user.

#### Query Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `per_page` | integer | Number of notifications per page (ignored when view_all=true) | 15 |
| `view_all` | boolean | If true, returns only 20 items without pagination | false |
| `unread_only` | boolean | Filter only unread notifications | false |
| `type` | string | Filter by notification type | null |
| `date_from` | date | Filter notifications from date (YYYY-MM-DD) | null |
| `date_to` | date | Filter notifications to date (YYYY-MM-DD) | null |

#### Example Requests

**Paginated Request:**
```bash
GET /api/notifications?per_page=20&unread_only=true&type=TaskEscalationNotification
```

**View All Request (20 items, no pagination):**
```bash
GET /api/notifications?view_all=true&unread_only=true
```

#### Example Responses

**Paginated Response:**
```json
{
    "status": true,
    "message": "Notifications retrieved successfully",
    "data": {
        "data": [
            {
                "id": "550e8400-e29b-41d4-a716-446655440000",
                "type": "App\\Notifications\\Tenant\\TaskEscalationNotification",
                "type_display": "Task Escalation",
                "title": "Task Escalation: Complete Project Review",
                "message": "Task \"Complete Project Review\" is 2 hours overdue and requires immediate attention.",
                "data": {
                    "task_id": 123,
                    "task_title": "Complete Project Review",
                    "task_description": "Review the project documentation",
                    "due_date": "Dec 15, 2024 at 2:00 PM",
                    "hours_overdue": 2,
                    "priority": "High",
                    "assigned_to": "John Doe",
                    "message": "Task \"Complete Project Review\" is 2 hours overdue and requires immediate attention.",
                    "type": "task_escalation",
                    "action_url": "/tasks/123"
                },
                "is_read": false,
                "read_at": null,
                "created_at": "2024-12-15 16:00:00",
                "created_at_human": "2 hours ago",
                "time_ago": "2 hours ago",
                "action_url": "/tasks/123",
                "icon": "exclamation-triangle",
                "priority": "high",
                "category": "tasks"
            }
        ],
        "current_page": 1,
        "last_page": 3,
        "per_page": 15,
        "total": 42
    }
}
```

**View All Response (no pagination):**
```json
{
    "status": true,
    "message": "Notifications retrieved successfully",
    "data": [
        {
            "id": "550e8400-e29b-41d4-a716-446655440000",
            "type": "App\\Notifications\\Tenant\\TaskEscalationNotification",
            "type_display": "Task Escalation",
            "title": "Task Escalation: Complete Project Review",
            "message": "Task \"Complete Project Review\" is 2 hours overdue and requires immediate attention.",
            "is_read": false,
            "created_at": "2024-12-15 16:00:00",
            "created_at_human": "2 hours ago",
            "action_url": "/tasks/123",
            "icon": "exclamation-triangle",
            "priority": "high",
            "category": "tasks"
        }
    ]
}
```

### 2. Get Unread Count

**GET** `/api/notifications/unread-count`

Get the count of unread notifications for the authenticated user.

#### Example Response

```json
{
    "status": true,
    "message": "Unread count retrieved successfully",
    "data": {
        "unread_count": 5
    }
}
```

### 3. Get Notification Statistics

**GET** `/api/notifications/statistics`

Get comprehensive statistics about the user's notifications.

#### Example Response

```json
{
    "status": true,
    "message": "Notification statistics retrieved successfully",
    "data": {
        "total": 25,
        "unread": 5,
        "read": 20,
        "by_type": {
            "App\\Notifications\\Tenant\\TaskEscalationNotification": 3,
            "App\\Notifications\\Tenant\\TaskReminderNotification": 8,
            "App\\Notifications\\Tenant\\TaskAssignmentNotification": 2,
            "App\\Notifications\\Tenant\\DealNotification": 12
        }
    }
}
```

### 4. Get Recent Notifications

**GET** `/api/notifications/recent`

Get the most recent notifications (limited number).

#### Query Parameters

| Parameter | Type | Description | Default |
|-----------|------|-------------|---------|
| `limit` | integer | Number of recent notifications to retrieve | 10 |

#### Example Request

```bash
GET /api/notifications/recent?limit=5
```

#### Example Response

```json
{
    "status": true,
    "message": "Recent notifications retrieved successfully",
    "data": [
        {
            "id": "550e8400-e29b-41d4-a716-446655440000",
            "type": "App\\Notifications\\Tenant\\TaskEscalationNotification",
            "type_display": "Task Escalation",
            "title": "Task Escalation: Complete Project Review",
            "message": "Task \"Complete Project Review\" is 2 hours overdue and requires immediate attention.",
            "is_read": false,
            "created_at": "2024-12-15 16:00:00",
            "created_at_human": "2 hours ago",
            "action_url": "/tasks/123",
            "icon": "exclamation-triangle",
            "priority": "high",
            "category": "tasks"
        }
    ]
}
```

### 5. Show Specific Notification

**GET** `/api/notifications/{id}`

Get details of a specific notification.

#### Example Response

```json
{
    "status": true,
    "message": "Notification retrieved successfully",
    "data": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "type": "App\\Notifications\\Tenant\\TaskEscalationNotification",
        "type_display": "Task Escalation",
        "title": "Task Escalation: Complete Project Review",
        "message": "Task \"Complete Project Review\" is 2 hours overdue and requires immediate attention.",
        "data": {
            "task_id": 123,
            "task_title": "Complete Project Review",
            "task_description": "Review the project documentation",
            "due_date": "Dec 15, 2024 at 2:00 PM",
            "hours_overdue": 2,
            "priority": "High",
            "assigned_to": "John Doe",
            "action_url": "/tasks/123"
        },
        "is_read": false,
        "read_at": null,
        "created_at": "2024-12-15 16:00:00",
        "created_at_human": "2 hours ago",
        "time_ago": "2 hours ago",
        "action_url": "/tasks/123",
        "icon": "exclamation-triangle",
        "priority": "high",
        "category": "tasks"
    }
}
```

### 6. Mark Notification as Read

**PATCH** `/api/notifications/{id}/mark-read`

Mark a specific notification as read.

#### Example Response

```json
{
    "status": true,
    "message": "Notification marked as read successfully",
    "data": null
}
```

### 7. Mark All Notifications as Read

**PATCH** `/api/notifications/mark-all-read`

Mark all notifications as read for the authenticated user.

#### Example Response

```json
{
    "status": true,
    "message": "All notifications marked as read successfully",
    "data": null
}
```

### 8. Delete Specific Notification

**DELETE** `/api/notifications/{id}`

Delete a specific notification.

#### Example Response

```json
{
    "status": true,
    "message": "Notification deleted successfully",
    "data": null
}
```

### 9. Delete All Notifications

**DELETE** `/api/notifications/delete-all`

Delete all notifications for the authenticated user.

#### Example Response

```json
{
    "status": true,
    "message": "All notifications deleted successfully",
    "data": null
}
```

## Notification Types

The system supports various notification types with specific display formatting:

### Task Escalation Notifications
- **Type**: `App\Notifications\Tenant\TaskEscalationNotification`
- **Icon**: `exclamation-triangle`
- **Priority**: `high`
- **Category**: `tasks`

### Task Reminder Notifications
- **Type**: `App\Notifications\Tenant\TaskReminderNotification`
- **Icon**: `clock`
- **Priority**: `medium`
- **Category**: `tasks`

### Task Assignment Notifications
- **Type**: `App\Notifications\Tenant\TaskAssignmentNotification`
- **Icon**: `user-plus`
- **Priority**: `medium`
- **Category**: `tasks`

### Task Status Change Notifications
- **Type**: `App\Notifications\Tenant\TaskStatusChangeNotification`
- **Icon**: `check-circle`
- **Priority**: `low`
- **Category**: `tasks`

### Deal Notifications
- **Type**: `App\Notifications\Tenant\DealNotification`
- **Icon**: `handshake`
- **Priority**: `high`
- **Category**: `deals`

### Opportunity Notifications
- **Type**: `App\Notifications\Tenant\OpportunityNotification`
- **Icon**: `trending-up`
- **Priority**: `high`
- **Category**: `opportunities`

### Client Notifications
- **Type**: `App\Notifications\Tenant\ClientNotification`
- **Icon**: `users`
- **Priority**: `medium`
- **Category**: `clients`

## Error Responses

All endpoints return consistent error responses:

```json
{
    "status": false,
    "message": "Error message description",
    "data": null
}
```

### Common HTTP Status Codes

- `200` - Success
- `400` - Bad Request (validation errors, not found)
- `401` - Unauthorized (invalid or missing token)
- `404` - Not Found (notification not found)
- `500` - Internal Server Error

## Usage Examples

### Frontend Integration

#### JavaScript/TypeScript Example

```javascript
// Get notifications with filters (paginated)
async function getNotifications(filters = {}) {
    const params = new URLSearchParams(filters);
    const response = await fetch(`/api/notifications?${params}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        }
    });
    return await response.json();
}

// Get all notifications (20 items, no pagination)
async function getAllNotifications(filters = {}) {
    const params = new URLSearchParams({ ...filters, view_all: 'true' });
    const response = await fetch(`/api/notifications?${params}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        }
    });
    return await response.json();
}

// Mark notification as read
async function markAsRead(notificationId) {
    const response = await fetch(`/api/notifications/${notificationId}/mark-read`, {
        method: 'PATCH',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        }
    });
    return await response.json();
}

// Get unread count for badge
async function getUnreadCount() {
    const response = await fetch('/api/notifications/unread-count', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        }
    });
    const data = await response.json();
    return data.data.unread_count;
}
```

#### React Hook Example

```jsx
import { useState, useEffect } from 'react';

function useNotifications() {
    const [notifications, setNotifications] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [loading, setLoading] = useState(false);

    const fetchNotifications = async (filters = {}, viewAll = false) => {
        setLoading(true);
        try {
            const response = viewAll ? 
                await getAllNotifications(filters) : 
                await getNotifications(filters);
            
            // Handle different response structures
            if (viewAll) {
                setNotifications(response.data);
            } else {
                setNotifications(response.data.data);
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchUnreadCount = async () => {
        try {
            const count = await getUnreadCount();
            setUnreadCount(count);
        } catch (error) {
            console.error('Error fetching unread count:', error);
        }
    };

    const markAsRead = async (notificationId) => {
        try {
            await markAsRead(notificationId);
            setNotifications(prev => 
                prev.map(n => 
                    n.id === notificationId 
                        ? { ...n, is_read: true, read_at: new Date().toISOString() }
                        : n
                )
            );
            setUnreadCount(prev => Math.max(0, prev - 1));
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    };

    useEffect(() => {
        fetchNotifications();
        fetchUnreadCount();
    }, []);

    return {
        notifications,
        unreadCount,
        loading,
        fetchNotifications,
        markAsRead,
        refreshUnreadCount: fetchUnreadCount
    };
}
```

## Best Practices

1. **Polling**: Use the unread count endpoint to check for new notifications periodically
2. **Real-time Updates**: Consider implementing WebSocket connections for real-time notifications
3. **Caching**: Cache notification data on the frontend to reduce API calls
4. **Error Handling**: Always handle API errors gracefully
5. **Pagination**: Use pagination for large notification lists
6. **Filtering**: Use filters to improve performance and user experience

## Security Considerations

- All endpoints require authentication
- Users can only access their own notifications
- Input validation is performed on all parameters
- SQL injection protection through Eloquent ORM
- XSS protection through proper data sanitization
