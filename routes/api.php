<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\AccountDetailsController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminSystemMessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ScholarTasksController;
use App\Http\Controllers\ScholarPortfolioController;
use App\Http\Controllers\ScholarRequestApplicationController;
use App\Http\Controllers\ScholarController;
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
    Route::post('login', [AuthController::class, 'login'])->name('app.login');
    Route::post('forgot_password', [AuthController::class, 'forgot_password']);
    
    Route::prefix('/authenticate/')->group(function () {
        Route::post('auth', [AuthController::class, 'authenticate']);
        Route::post('user', [AuthController::class, 'getAuthenticatedUser']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('invalidate', [AuthController::class, 'deauthenticate']);
    });

    Route::prefix('/comments/')->group(function () {
        Route::post('create', [CommentsController::class, 'create']);
        Route::post('retrieveByParameter', [CommentsController::class, 'retrieveByParameter']);
        Route::post('retrieveAll', [CommentsController::class, 'retrieveAll']);
        Route::post('update', [CommentsController::class, 'update']);
        Route::post('delete', [CommentsController::class, 'delete']);
        Route::post('forgot_password', [AuthController::class, 'forgotPassword']);
    });

    Route::prefix('/user/')->group(function () {
        Route::post('retrieveAll', [UserController::class, 'retrieveAll']);
        Route::post('retrieveByParameter', [UserController::class, 'retrieveByParameter']);
    });
    // 2-20-24 update statements are not yet done, new update method
    Route::prefix('/account_details/')->group(function () {
        Route::post('create', [AccountDetailsController::class, 'create']);
        Route::post('delete', [AccountDetailsController::class, 'delete']);
        Route::post('update', [AccountDetailsController::class, 'update']);
        Route::patch('patch/{id}', [AccountDetailsController::class, 'patch']);
        Route::post('retrieveByParameter', [AccountDetailsController::class, 'retrieveByParameter']);
        Route::post('retrieveAll', [AccountDetailsController::class, 'retrieveAll']);
    });

    Route::prefix('/leave_application/')->group(function () {
        Route::post('create', [LeaveApplicationController::class, 'create']);
        Route::post('retrieveByParameter', [LeaveApplicationController::class, 'retrieveByParameter']);
        Route::post('retrieveAll', [LeaveApplicationController::class, 'retrieveAll']);
        Route::post('update', [LeaveApplicationController::class, 'update']);
        Route::post('delete', [LeaveApplicationController::class, 'delete']);
    });

    Route::prefix('/admin_system_message/')->group(function () {
        Route::post('create', [AdminSystemMessageController::class, 'create']);
        Route::post('retrieveByParameter', [AdminSystemMessageController::class, 'retrieveByParameter']);
        Route::post('retrieveAll', [AdminSystemMessageController::class, 'retrieveAll']);
        Route::post('update', [AdminSystemMessageController::class, 'update']);
        Route::post('delete', [AdminSystemMessageController::class, 'delete']);
    });
    Route::prefix('/scholar/')->group(function () {
        Route::post('create', [ScholarController::class, 'create']);
        Route::post('retrieveByParameter', [ScholarController::class, 'retrieveByParameter']);
        Route::post('retrieveAll', [ScholarController::class, 'retrieveAll']);
        Route::post('update', [ScholarController::class, 'update']);
        Route::post('delete', [ScholarController::class, 'delete']);
    });
    Route::prefix('/scholar_portfolio/')->group(function () {
        Route::post('create', [ScholarPortfolioController::class, 'create']);
        Route::post('retrieveByParameter', [ScholarPortfolioController::class, 'retrieveByParameter']);
        Route::post('retrieveAll', [ScholarPortfolioController::class, 'retrieveAll']);
        Route::post('update', [ScholarPortfolioController::class, 'update']);
        Route::post('delete', [ScholarPortfolioController::class, 'delete']);
    });
    Route::prefix('/scholar_request/')->group(function () {
        Route::post('create', [ScholarRequestApplicationController::class, 'create']);
        Route::post('retrieveByParameter', [ScholarRequestApplicationController::class, 'retrieveByParameter']);
        Route::post('retrieveAll', [ScholarRequestApplicationController::class, 'retrieveAll']);
        Route::post('update', [ScholarRequestApplicationController::class, 'update']);
        Route::post('delete', [ScholarRequestApplicationController::class, 'delete']);
    });
    Route::prefix('/scholar_tasks/')->group(function () {
        Route::post('create', [ScholarTasksController::class, 'create']);
        Route::post('retrieveByParameter', [ScholarTasksController::class, 'retrieveByParameter']);
        Route::post('retrieveAll', [ScholarTasksController::class, 'retrieveAll']);
        Route::post('update', [ScholarTasksController::class, 'update']);
        Route::post('delete', [ScholarTasksController::class, 'delete']);
    });
    
    
    Route::prefix('/notification/')->group(function () {
        Route::post('create', [NotificationController::class, 'create']);
        Route::post('retrieveByParameter', [NotificationController::class, 'retrieveByParameter']);
        Route::post('retrieveAll', [NotificationController::class, 'retrieveAll']);
        Route::post('update', [NotificationController::class, 'update']);
        Route::post('delete', [NotificationController::class, 'delete']);
    });
});
