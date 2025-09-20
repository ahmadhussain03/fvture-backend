<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController
{
    /**
     * Get events with DJs
     * 
     * Retrieve a paginated list of events with their associated DJs. This endpoint supports various filtering options
     * to help you find specific events based on date, DJ, or search terms.
     * 
     * **Authentication Required**: This endpoint requires a valid Sanctum token. Include the token in the Authorization header as `Bearer {token}`.
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @queryParam page integer The page number for pagination. Example: 1
     * @queryParam per_page integer Number of items per page (maximum 50). Example: 15
     * @queryParam search string Search term to filter events by name and description. Example: music festival
     * @queryParam upcoming boolean Filter for upcoming events only (events after current date). Example: true
     * @queryParam dj_id integer Filter events by specific DJ ID. Example: 1
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Summer Music Festival",
     *       "description": "A great music festival featuring top DJs from around the world...",
     *       "event_date_time": "2024-06-15T18:00:00.000000Z",
     *       "video": "https://youtube.com/watch?v=example",
     *       "banner_image": "https://s3.amazonaws.com/bucket/event-banners/festival.jpg",
     *       "other_information": "Additional event details including parking, food vendors, and more...",
     *       "djs": [
     *         {
     *           "id": 1,
     *           "name": "DJ John",
     *           "description": "Professional DJ with 10 years experience in electronic music",
     *           "image": "https://s3.amazonaws.com/bucket/djs/images/dj-john.jpg",
     *           "created_at": "2024-01-01T00:00:00.000000Z",
     *           "updated_at": "2024-01-01T00:00:00.000000Z"
     *         },
     *         {
     *           "id": 2,
     *           "name": "DJ Sarah",
     *           "description": "Award-winning DJ specializing in house music",
     *           "image": "https://s3.amazonaws.com/bucket/djs/images/dj-sarah.jpg",
     *           "created_at": "2024-01-01T00:00:00.000000Z",
     *           "updated_at": "2024-01-01T00:00:00.000000Z"
     *         }
     *       ],
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://localhost/api/events?page=1",
     *     "last": "http://localhost/api/events?page=5",
     *     "prev": null,
     *     "next": "http://localhost/api/events?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 5,
     *     "path": "http://localhost/api/events",
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
     *     "dj_id": ["The dj id must be a valid integer."]
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
        $query = Event::with(['djs']);

        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('upcoming') && $request->upcoming) {
            $query->where('event_date_time', '>', now());
        }

        if ($request->has('dj_id') && $request->dj_id) {
            $query->whereHas('djs', function ($q) use ($request) {
                $q->where('djs.id', $request->dj_id);
            });
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $events = $query->orderBy('event_date_time', 'asc')
                       ->paginate($perPage);

        return EventResource::collection($events)->response();
    }
}