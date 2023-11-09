<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/app/')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot_password', [AuthController::class, 'forgot_password']);
    
    Route::prefix('/comments/')->group(function () {
        Route::post('create', [CommentsController::class, 'create']);
        Route::post('retrievebyParameter', [CommentsController::class, 'retrievebyParameter']);
        Route::post('retrieveAll', [CommentsController::class, 'retrieveAll']);
        Route::post('update', [CommentsController::class, 'update']);
        Route::post('delete', [CommentsController::class, 'delete']);
    });

    Route::prefix('/leave_application/')->group(function () {
        Route::post('create', [LeaveApplicationController::class, 'create']);
        Route::post('retrievebyParameter', [LeaveApplicationController::class, 'retrievebyParameter']);
        Route::post('retrieveAll', [LeaveApplicationController::class, 'retrieveAll']);
        Route::post('update', [LeaveApplicationController::class, 'update']);
        Route::post('delete', [LeaveApplicationController::class, 'delete']);
    });
});

//testing
Route::get('comments', function() {
    return 'these are the comments';
});