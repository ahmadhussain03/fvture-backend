<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController
{
    /**
     * Register a new user
     * 
     * Create a new user account with app user type. This endpoint allows users to register for the mobile application.
     * Upon successful registration, you will receive a user object and an access token that can be used for authenticated requests.
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     * 
     * @response {
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "user_type": "app",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   },
     *   "token": "1|abcdef1234567890abcdef1234567890abcdef12"
     * }
     * 
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "name": ["The name field is required."],
     *     "email": ["The email has already been taken.", "The email field is required."],
     *     "password": ["The password must be at least 8 characters.", "The password field is required."],
     *     "password_confirmation": ["The password confirmation does not match.", "The password confirmation field is required."]
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
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'app',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     * 
     * Authenticate a user and return an access token. Use this token in the Authorization header for protected endpoints.
     * The token should be included in subsequent requests as `Authorization: Bearer {token}`.
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     * 
     * @response {
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "user_type": "app",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   },
     *   "token": "1|abcdef1234567890abcdef1234567890abcdef12"
     * }
     * 
     * @response 401 {
     *   "message": "The provided credentials are incorrect."
     * }
     * 
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email field is required.", "The email must be a valid email address."],
     *     "password": ["The password field is required.", "The password must be at least 8 characters."]
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
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     * 
     * Revoke the current access token. After logout, the token cannot be used for future requests.
     * This endpoint requires authentication - include your token in the Authorization header.
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @response {
     *   "message": "Successfully logged out"
     * }
     * 
     * @response 401 {
     *   "message": "Unauthenticated."
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
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}