<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(public NotificationService $notificationService) {}

    /**
     * Get notifications for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['unread_only', 'type', 'date_from', 'date_to']);
            $viewAll = $request->boolean('view_all', false);
            
            if ($viewAll) {
                // Get paginated results
                $perPage = $request->get('per_page', 15);
                $notifications = $this->notificationService->getUserNotifications($filters, $perPage, true);
                $data = NotificationResource::collection($notifications)->response()->getData(true);
            } else {
                // Get only 20 items without pagination
                $notifications = $this->notificationService->getUserNotifications($filters, 20, false);
                $data = NotificationResource::collection($notifications);
            }
            
            return apiResponse($data, 'Notifications retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(): JsonResponse
    {
        try {
            $count = $this->notificationService->getUnreadCount();
            
            return apiResponse(['unread_count' => $count], 'Unread count retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->notificationService->getNotificationStats();
            
            return apiResponse($stats, 'Notification statistics retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Get recent notifications
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $notifications = $this->notificationService->getRecentNotifications($limit);
            
            $data = NotificationResource::collection($notifications);
            
            return apiResponse($data, 'Recent notifications retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(string $id): JsonResponse
    {
        try {
            $this->notificationService->markAsRead($id);
            
            return apiResponse(null, 'Notification marked as read successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 400);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $this->notificationService->markAllAsRead();
            
            return apiResponse(null, 'All notifications marked as read successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Delete a specific notification
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->notificationService->deleteNotification($id);
            
            return apiResponse(null, 'Notification deleted successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 400);
        }
    }

    /**
     * Delete all notifications
     */
    public function deleteAll(): JsonResponse
    {
        try {
            $this->notificationService->deleteAllNotifications();
            
            return apiResponse(null, 'All notifications deleted successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }

    /**
     * Show a specific notification
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()->find($id);
            
            if (!$notification) {
                return apiResponse(message: 'Notification not found', code: 404);
            }
            
            $data = new NotificationResource($notification);
            
            return apiResponse($data, 'Notification retrieved successfully');
        } catch (Exception $e) {
            return apiResponse(message: $e->getMessage(), code: 500);
        }
    }
}
