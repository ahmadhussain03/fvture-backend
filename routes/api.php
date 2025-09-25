<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\ArtistController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
  // Private routes
  Route::get('blogs', [BlogController::class, 'index']);
  Route::get('events', [EventController::class, 'index']);
  Route::get('artists', [ArtistController::class, 'index']);
  Route::get('artists/{id}', [ArtistController::class, 'show']);
  Route::get('announcements', [AnnouncementController::class, 'index']);
  Route::get('gallery', [GalleryController::class, 'index']);
  
  // Event album routes
  Route::get('events/{id}/album', [EventController::class, 'album']);
  
  // Notification routes
  Route::get('notifications', [NotificationController::class, 'index']);
  Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
  Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

});
