<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CommentController;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Welcome to the admin dashboard']);
    });
    Route::apiResource('complaints', ComplaintController::class);
    Route::post('/complaint-report', [ComplaintController::class, 'report']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/complaints/{id}/comments', [CommentController::class, 'store']);
    Route::get('/complaints/{id}/comments', [CommentController::class, 'complaintComments']);
    Route::get('category-list', [CategoryController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::apiResource('categories', CategoryController::class);
    Route::get('/user-list', [AuthController::class, 'userList']);
    Route::post('/create-user', [AuthController::class, 'createUser']);
    Route::put('/complaintStatus/change/{id}', [ComplaintController::class, 'changeStatus']);
    Route::get('/priority-report', [ComplaintController::class, 'priorityReport']);
    Route::get('/status-report', [ComplaintController::class, 'statusReport']);
    Route::get('/category-report', [ComplaintController::class, 'categoryReport']);
    Route::get('/resolution-time-report', [ComplaintController::class, 'resolutionReport']);
    Route::get('/complaint-trend-report', [ComplaintController::class, 'complaintTrend']);
});

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {

    Route::get('/user/dashboard', function () {
        return response()->json(['message' => 'test Welcome to the user dashboard']);
    });
});

Route::middleware('auth:sanctum')->get('/auth/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out successfully'], 200);
});
