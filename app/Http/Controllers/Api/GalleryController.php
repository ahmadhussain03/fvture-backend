<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\GalleryResource;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryController
{
    /**
     * Get gallery items
     * 
     * Retrieve a paginated list of gallery items (images and videos). This endpoint supports
     * various filtering options to help you find specific gallery items.
     * 
     * **Authentication Required**: This endpoint requires a valid Sanctum token. Include the token in the Authorization header as `Bearer {token}`.
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @queryParam page integer The page number for pagination. Example: 1
     * @queryParam per_page integer Number of items per page (maximum 50). Example: 15
     * @queryParam search string Search term to filter gallery items by title and description. Example: concert
     * @queryParam type string Filter by file type (image or video). Example: image
     * @queryParam event_id integer Filter gallery items by specific event ID. Example: 1
     * 
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Concert Highlights",
     *       "description": "Amazing moments from the concert",
     *       "file_url": "https://s3.amazonaws.com/bucket/gallery/concert-highlights.jpg",
     *       "type": "image",
     *       "file_size": "2048576",
     *       "formatted_file_size": "2.0 MB",
     *       "mime_type": "image/jpeg",
     *       "event": {
     *         "id": 1,
     *         "name": "Summer Music Festival",
     *         "description": "A great music festival...",
     *         "event_date_time": "2024-06-15T18:00:00.000000Z",
     *         "video": "https://youtube.com/watch?v=example",
     *         "banner_image": "https://s3.amazonaws.com/bucket/event-banners/festival.jpg",
     *         "other_information": "Additional event details...",
     *         "djs": [],
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       },
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://localhost/api/gallery?page=1",
     *     "last": "http://localhost/api/gallery?page=5",
     *     "prev": null,
     *     "next": "http://localhost/api/gallery?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 5,
     *     "path": "http://localhost/api/gallery",
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
     *     "page": ["The page must be at least 1."],
     *     "type": ["The type must be either image or video."]
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
        $query = Gallery::with(['event']);

        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $gallery = $query->orderBy('created_at', 'desc')
                        ->paginate($perPage);

        return GalleryResource::collection($gallery)->response();
    }
}
