# Authentication

The FVTure API uses **Laravel Sanctum** for authentication, which provides a simple token-based authentication system.

## 🔑 How Authentication Works

1. **Register** a new account to get started (returns user data + token)
2. **Login** with your credentials to receive an access token
3. **Include the token** in the `Authorization` header for protected requests
4. **Logout** when you're done to revoke the token

### Token Format
- **Format**: `{id}|{random_string}`
- **Example**: `1|abcdef1234567890abcdef1234567890abcdef12`
- **Length**: 64 characters (excluding ID and pipe)
- **Usage**: Include as `Authorization: Bearer {token}` in headers

## 📝 Authentication Flow

### Step 1: Register a New User

```bash
curl -X POST {{ config('app.url') }}/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "user_type": "app",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|abcdef1234567890abcdef1234567890abcdef12"
}
```

### Step 2: Login to Get Token

```bash
curl -X POST {{ config('app.url') }}/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "user_type": "app",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "token": "1|abcdef1234567890abcdef1234567890abcdef12"
}
```

### Step 3: Use Token in Requests

```bash
curl -X GET {{ config('app.url') }}/api/blogs \
  -H "Authorization: Bearer 1|abcdef1234567890abcdef1234567890abcdef12" \
  -H "Accept: application/json"
```

### Step 4: Logout (Revoke Token)

```bash
curl -X POST {{ config('app.url') }}/api/auth/logout \
  -H "Authorization: Bearer 1|abcdef1234567890abcdef1234567890abcdef12" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "message": "Successfully logged out"
}
```

## 🔧 Token Management

### Token Format
- Tokens are in the format: `{id}|{random_string}`
- Example: `1|abcdef1234567890abcdef1234567890abcdef12`
- Tokens are 64 characters long (excluding the ID and pipe)

### Token Storage
- Store the token securely in your application
- Include it in the `Authorization` header as `Bearer {token}`
- Tokens do not expire automatically (you can implement expiration if needed)

### Token Revocation
- Use the `/api/auth/logout` endpoint to revoke the current token
- Revoked tokens cannot be used for future requests
- You can create multiple tokens for the same user

## 🚨 Error Handling

### Common Error Responses

#### 401 Unauthenticated
```json
{
  "message": "Unauthenticated."
}
```
**Causes:**
- Missing Authorization header
- Invalid token format
- Expired or revoked token
- Token not found in database

#### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```
**Causes:**
- Valid token but insufficient permissions
- User account is disabled

#### 422 Validation Errors
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken.", "The email field is required."],
    "password": ["The password must be at least 8 characters.", "The password field is required."],
    "password_confirmation": ["The password confirmation does not match."]
  }
}
```

#### 429 Rate Limiting
```json
{
  "message": "Too Many Attempts."
}
```
**Causes:**
- Too many requests in a short time period
- Exceeded rate limit for authentication endpoints

#### 500 Server Error
```json
{
  "message": "Server Error"
}
```
**Causes:**
- Internal server error
- Database connection issues
- Unexpected application errors

### Troubleshooting Authentication Issues

#### 1. Token Not Working
- **Check token format**: Should be `{id}|{random_string}`
- **Verify Authorization header**: Must be `Authorization: Bearer {token}`
- **Ensure token is not expired**: Check if token was revoked via logout
- **Test with a fresh token**: Try logging in again to get a new token

#### 2. Getting 401 Unauthenticated
- **Missing header**: Ensure `Authorization: Bearer {token}` is included
- **Wrong format**: Token should start with `Bearer ` (note the space)
- **Invalid token**: Token might be malformed or corrupted
- **Token revoked**: User might have logged out, revoking the token

#### 3. Getting 422 Validation Errors
- **Check required fields**: Ensure all required fields are provided
- **Validate email format**: Email must be a valid email address
- **Password requirements**: Password must be at least 8 characters
- **Password confirmation**: Must match the password field exactly

#### 4. Testing Your Token
```bash
# Test if your token works
curl -X GET {{ config('app.url') }}/api/blogs \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json" \
  -v
```

## 🧪 Testing Authentication

### Using Postman
1. Import the Postman collection
2. Set up environment variables:
   - `base_url`: `{{ config('app.url') }}/api`
   - `token`: (leave empty initially)
3. Run the "Register" request
4. Copy the token from the response
5. Set the `token` environment variable
6. All subsequent requests will automatically use the token

### Using cURL
```bash
# Set your token as an environment variable
export TOKEN="1|abcdef1234567890abcdef1234567890abcdef12"

# Use in requests
curl -X GET {{ config('app.url') }}/api/blogs \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### Using JavaScript/Fetch
```javascript
const token = '1|abcdef1234567890abcdef1234567890abcdef12';

// GET request with token
fetch('{{ config('app.url') }}/api/blogs', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})
.then(response => {
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return response.json();
})
.then(data => console.log(data))
.catch(error => console.error('Error:', error));

// POST request with token (e.g., logout)
fetch('{{ config('app.url') }}/api/auth/logout', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

### Using PHP/cURL
```php
$token = '1|abcdef1234567890abcdef1234567890abcdef12';

// GET request with token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, '{{ config('app.url') }}/api/blogs');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    print_r($data);
} else {
    echo "Error: HTTP $httpCode - $response";
}
```

### Using Python/Requests
```python
import requests

token = '1|abcdef1234567890abcdef1234567890abcdef12'
headers = {
    'Authorization': f'Bearer {token}',
    'Accept': 'application/json',
    'Content-Type': 'application/json'
}

# GET request with token
response = requests.get('{{ config('app.url') }}/api/blogs', headers=headers)

if response.status_code == 200:
    data = response.json()
    print(data)
else:
    print(f"Error: {response.status_code} - {response.text}")

# POST request with token (e.g., logout)
logout_response = requests.post('{{ config('app.url') }}/api/auth/logout', headers=headers)
print(logout_response.json())
```

### Using Axios (JavaScript)
```javascript
import axios from 'axios';

const token = '1|abcdef1234567890abcdef1234567890abcdef12';

// Create axios instance with default headers
const api = axios.create({
  baseURL: '{{ config('app.url') }}/api',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// GET request
api.get('/blogs')
  .then(response => console.log(response.data))
  .catch(error => console.error('Error:', error.response?.data || error.message));

// POST request
api.post('/auth/logout')
  .then(response => console.log(response.data))
  .catch(error => console.error('Error:', error.response?.data || error.message));
```

## 🔒 Security Best Practices

1. **Never expose tokens** in client-side code or logs
2. **Use HTTPS** in production to protect tokens in transit
3. **Implement token refresh** if needed for long-lived sessions
4. **Log out** when the user is done to revoke tokens
5. **Validate tokens** on the server side for sensitive operations

## 📋 User Types

The API supports different user types:
- `app` - Regular app users (created via API registration)
- `admin` - Admin users (created via admin panel)

API registration always creates users with `user_type: 'app'`.