# FVTure API Documentation

Welcome to the FVTure API! This comprehensive API provides access to blogs, events, and DJ information for the FVTure platform.

## 🚀 Getting Started

The FVTure API is built with Laravel and uses **Laravel Sanctum** for authentication. All API endpoints are prefixed with `/api/`.

### Base URL
```
{{ config('app.url') }}/api
```

### Authentication
This API uses **Laravel Sanctum** for token-based authentication. To access protected endpoints, you need to:

1. Register a new account using `/api/auth/register`
2. Login to get your access token using `/api/auth/login`
3. Include the token in the `Authorization` header for protected requests

### Example Authentication Flow

```bash
# 1. Register a new user
curl -X POST {{ config('app.url') }}/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Response: You'll get a user object and token
# {
#   "data": { "id": 1, "name": "John Doe", "email": "john@example.com", ... },
#   "token": "1|abcdef1234567890abcdef1234567890abcdef12"
# }

# 2. Login to get token (if you already have an account)
curl -X POST {{ config('app.url') }}/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'

# Response: You'll get a user object and token
# {
#   "data": { "id": 1, "name": "John Doe", "email": "john@example.com", ... },
#   "token": "1|abcdef1234567890abcdef1234567890abcdef12"
# }

# 3. Use token in subsequent requests
curl -X GET {{ config('app.url') }}/api/blogs \
  -H "Authorization: Bearer 1|abcdef1234567890abcdef1234567890abcdef12" \
  -H "Accept: application/json"

# 4. Logout when done (revokes the token)
curl -X POST {{ config('app.url') }}/api/auth/logout \
  -H "Authorization: Bearer 1|abcdef1234567890abcdef1234567890abcdef12" \
  -H "Accept: application/json"
```

## 📋 Available Endpoints

### Authentication
- `POST /api/auth/register` - Register a new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout user (requires authentication)

### Public Content
- `GET /api/blogs` - Get paginated blogs with filtering options
- `GET /api/events` - Get events with DJs and filtering options

## 🔍 Query Parameters

### Blogs Endpoint
- `page` - Page number (default: 1)
- `per_page` - Items per page, max 50 (default: 15)
- `search` - Search in title and content
- `category_id` - Filter by category ID
- `tag_id` - Filter by tag ID
- `published` - Filter by published status (true/false)

### Events Endpoint
- `page` - Page number (default: 1)
- `per_page` - Items per page, max 50 (default: 15)
- `search` - Search in name and description
- `upcoming` - Filter for upcoming events only (true/false)
- `dj_id` - Filter by specific DJ ID

## 📸 Image Handling

All images are stored on AWS S3 and returned as full URLs:
- Blog banner images: `banner_image` field
- Event banner images: `banner_image` field
- DJ profile images: `image` field

## 🔒 Rate Limiting

API requests are rate limited to prevent abuse. If you exceed the rate limit, you'll receive a `429 Too Many Requests` response.

## 📝 Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "data": {
    // Response data here
  },
  "links": {
    // Pagination links (for paginated endpoints)
  },
  "meta": {
    // Pagination metadata (for paginated endpoints)
  }
}
```

### Error Response
```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

## 🧪 Testing the API

You can test the API using:
1. **Interactive Documentation** - Use the "Try it out" buttons in this documentation
2. **Postman Collection** - Download the generated Postman collection
3. **cURL** - Use the provided cURL examples
4. **Your preferred HTTP client** - All examples are provided in multiple languages

## 📚 Additional Resources

- **OpenAPI Specification**: Available at `/docs/openapi.yaml`
- **Postman Collection**: Available at `/storage/app/private/scribe/collection.json`
- **GitHub Repository**: [Link to your repository]

## 🆘 Support

If you encounter any issues or have questions:
1. Check the error response for detailed information
2. Verify your authentication token is valid
3. Ensure you're using the correct HTTP methods and endpoints
4. Contact support at [your-support-email]

---

*This documentation is automatically generated and updated with each API change.*