<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController
{
    /**
     * Get announcements
     * 
     * Retrieve a paginated list of announcements. This endpoint supports various filtering options
     * to help you find specific announcements based on search terms.
     * 
     * **Authentication Required**: This endpoint requires a valid Sanctum token. Include the token in the Authorization header as `Bearer {token}`.
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @queryParam page integer The page number for pagination. Example: 1
     * @queryParam per_page integer Number of items per page (maximum 50). Example: 15
     * @queryParam search string Search term to filter announcements by title and description. Example: important
     * 
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Important Update",
     *       "description": "We have an important update regarding our services...",
     *       "image": "https://s3.amazonaws.com/bucket/announcements/images/update.jpg",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://localhost/api/announcements?page=1",
     *     "last": "http://localhost/api/announcements?page=5",
     *     "prev": null,
     *     "next": "http://localhost/api/announcements?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 5,
     *     "path": "http://localhost/api/announcements",
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
        $query = Announcement::query();

        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $announcements = $query->orderBy('created_at', 'desc')
                              ->paginate($perPage);

        return AnnouncementResource::collection($announcements)->response();
    }
}
