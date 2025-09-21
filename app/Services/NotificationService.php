<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationService extends BaseService
{
    public function getModel(): DatabaseNotification
    {
        return new DatabaseNotification();
    }

    /**
     * Get notifications for the authenticated user
     */
    public function getUserNotifications(array $filters = [], int $perPage = 15, bool $paginate = true)
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $query = $user->notifications();

        // Apply filters
        if (isset($filters['unread_only']) && $filters['unread_only']) {
            $query->whereNull('read_at');
        }

        if (isset($filters['type']) && $filters['type']) {
            $query->where('type', 'LIKE', '%' . $filters['type'] . '%');
        }

        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        if ($paginate) {
            return $query->paginate($perPage);
        } else {
            return $query->limit($perPage)->get();
        }
    }

    /**
     * Get unread notifications count for the authenticated user
     */
    public function getUnreadCount(): int
    {
        $user = Auth::user();
        
        if (!$user) {
            return 0;
        }

        return $user->unreadNotifications()->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $notificationId): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $notification = $user->notifications()->find($notificationId);
        
        if (!$notification) {
            throw new \Exception('Notification not found');
        }

        if ($notification->read_at) {
            return true; // Already read
        }

        $notification->markAsRead();
        return true;
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $user->unreadNotifications->markAsRead();
        return true;
    }

    /**
     * Delete a specific notification
     */
    public function deleteNotification(string $notificationId): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $notification = $user->notifications()->find($notificationId);
        
        if (!$notification) {
            throw new \Exception('Notification not found');
        }

        return $notification->delete();
    }

    /**
     * Delete all notifications for the authenticated user
     */
    public function deleteAllNotifications(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new \Exception('User not authenticated');
        }

        $user->notifications()->delete();
        return true;
    }

    /**
     * Get notification statistics for the authenticated user
     */
    public function getNotificationStats(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [
                'total' => 0,
                'unread' => 0,
                'read' => 0,
                'by_type' => []
            ];
        }

        $total = $user->notifications()->count();
        $unread = $user->unreadNotifications()->count();
        $read = $total - $unread;

        // Get count by type
        $byType = $user->notifications()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $read,
            'by_type' => $byType
        ];
    }

    /**
     * Get recent notifications (last 10)
     */
    public function getRecentNotifications(int $limit = 10): Collection
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect();
        }

        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
