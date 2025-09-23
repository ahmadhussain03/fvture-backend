<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController
{
    /**
     * Get user notifications
     * 
     * Retrieve a paginated list of notifications for the authenticated user. This endpoint supports
     * filtering by read status and provides options to mark notifications as read.
     * 
     * **Authentication Required**: This endpoint requires a valid Sanctum token. Include the token in the Authorization header as `Bearer {token}`.
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @queryParam page integer The page number for pagination. Example: 1
     * @queryParam per_page integer Number of items per page (maximum 50). Example: 15
     * @queryParam unread_only boolean Filter for unread notifications only. Example: true
     * 
     * @response {
     *   "data": [
     *     {
     *       "id": "123e4567-e89b-12d3-a456-426614174000",
     *       "type": "App\\Notifications\\AnnouncementNotification",
     *       "notifiable_type": "App\\Models\\User",
     *       "notifiable_id": 1,
     *       "data": {
     *         "announcement_id": 1,
     *         "title": "Important Update",
     *         "message": "We have an important update regarding our services...",
     *         "image": "https://s3.amazonaws.com/bucket/announcements/images/update.jpg"
     *       },
     *       "read_at": null,
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://localhost/api/notifications?page=1",
     *     "last": "http://localhost/api/notifications?page=5",
     *     "prev": null,
     *     "next": "http://localhost/api/notifications?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 5,
     *     "path": "http://localhost/api/notifications",
     *     "per_page": 15,
     *     "to": 15,
     *     "total": 75
     *   }
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * 
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "per_page": ["The per page must not be greater than 50."],
     *     "page": ["The page must be at least 1."]
     *   }
     * }
     * 
     * @response 429 {
     *   "message": "Too Many Attempts."
     * }
     * 
     * @response 500 {
     *   "message": "Server Error"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = $user->notifications();

        // Apply filters
        if ($request->has('unread_only') && $request->unread_only) {
            $query->whereNull('read_at');
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $notifications = $query->orderBy('created_at', 'desc')
                              ->paginate($perPage);

        return NotificationResource::collection($notifications)->response();
    }

    /**
     * Mark notification as read
     * 
     * Mark a specific notification as read for the authenticated user.
     * 
     * **Authentication Required**: This endpoint requires a valid Sanctum token. Include the token in the Authorization header as `Bearer {token}`.
     * 
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * 
     * @response 200 {
     *   "message": "Notification marked as read"
     * }
     * 
     * @response 404 {
     *   "message": "Notification not found"
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read
     * 
     * Mark all notifications as read for the authenticated user.
     * 
     * **Authentication Required**: This endpoint requires a valid Sanctum token. Include the token in the Authorization header as `Bearer {token}`.
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @response 200 {
     *   "message": "All notifications marked as read"
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
