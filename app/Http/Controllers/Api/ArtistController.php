<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\ArtistResource;
use App\Models\Artist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtistController
{
    /**
     * Get artists
     * 
     * Retrieve a paginated list of artists. This endpoint supports various filtering options
     * to help you find specific artists based on search terms.
     * 
     * **Authentication Required**: This endpoint requires a valid Sanctum token. Include the token in the Authorization header as `Bearer {token}`.
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @queryParam page integer The page number for pagination. Example: 1
     * @queryParam per_page integer Number of items per page (maximum 50). Example: 15
     * @queryParam search string Search term to filter artists by name and description. Example: john
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Artist John",
     *       "description": "Professional artist with 10 years experience in electronic music",
     *       "image": "https://s3.amazonaws.com/bucket/artists/images/artist-john.jpg",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://localhost/api/artists?page=1",
     *     "last": "http://localhost/api/artists?page=10",
     *     "prev": null,
     *     "next": "http://localhost/api/artists?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 10,
     *     "path": "http://localhost/api/artists",
     *     "per_page": 15,
     *     "to": 15,
     *     "total": 150
     *   }
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * 
     * @response 500 {
     *   "message": "Server Error"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Artist::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $artists = $query->orderBy('name', 'asc')
                        ->paginate($perPage);

        return ArtistResource::collection($artists)->response();
    }

    /**
     * Get specific artist
     * 
     * Retrieve details of a specific artist by ID.
     * 
     * **Authentication Required**: This endpoint requires a valid Sanctum token. Include the token in the Authorization header as `Bearer {token}`.
     * 
     * @param int $id Artist ID
     * @return JsonResponse
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "Artist John",
     *     "description": "Professional artist with 10 years experience in electronic music",
     *     "image": "https://s3.amazonaws.com/bucket/artists/images/artist-john.jpg",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 404 {
     *   "message": "Artist not found"
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function show(int $id): JsonResponse
    {
        $artist = Artist::findOrFail($id);
        
        return response()->json([
            'data' => new ArtistResource($artist)
        ]);
    }
}
