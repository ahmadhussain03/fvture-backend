<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\BlogResource;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController
{
    /**
     * Get paginated blogs
     * 
     * Retrieve a paginated list of published blogs with optional filtering and search capabilities.
     * This endpoint supports various query parameters to filter and search through blog posts.
     * 
     * **Authentication Required**: This endpoint requires a valid Sanctum token. Include the token in the Authorization header as `Bearer {token}`.
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @queryParam page integer The page number for pagination. Example: 1
     * @queryParam per_page integer Number of items per page (maximum 50). Example: 15
     * @queryParam search string Search term to filter blogs by title and content. Example: music
     * @queryParam category_id integer Filter blogs by specific category ID. Example: 1
     * @queryParam tag_id integer Filter blogs by specific tag ID. Example: 2
     * @queryParam published boolean Filter by published status. Defaults to true for public API. Example: true
     * 
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Sample Blog Post",
     *       "slug": "sample-blog-post",
     *       "content": "This is the blog content...",
     *       "banner_image": "https://s3.amazonaws.com/bucket/blog-banners/image.jpg",
     *       "is_published": true,
     *       "published_at": "2024-01-01T00:00:00.000000Z",
     *       "author": {
     *         "id": 1,
     *         "name": "John Doe",
     *         "email": "john@example.com",
     *         "user_type": "admin",
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       },
     *       "categories": [
     *         {
     *           "id": 1,
     *           "name": "Technology",
     *           "slug": "technology"
     *         }
     *       ],
     *       "tags": [
     *         {
     *           "id": 1,
     *           "name": "Laravel",
     *           "slug": "laravel"
     *         }
     *       ],
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://localhost/api/blogs?page=1",
     *     "last": "http://localhost/api/blogs?page=10",
     *     "prev": null,
     *     "next": "http://localhost/api/blogs?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "from": 1,
     *     "last_page": 10,
     *     "path": "http://localhost/api/blogs",
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
        $query = Blog::with(['user', 'categories', 'tags']);

        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('tag_id') && $request->tag_id) {
            $query->byTag($request->tag_id);
        }

        if ($request->has('published') && $request->published !== null) {
            if ($request->published) {
                $query->published();
            } else {
                $query->draft();
            }
        } else {
            // Default to published only for public API
            $query->published();
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $blogs = $query->orderBy('published_at', 'desc')
                      ->paginate($perPage);

        return BlogResource::collection($blogs)->response();
    }
}