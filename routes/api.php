<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\AccountDetailsController;
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
    Route::post('forgot_password', [AuthController::class, 'forgotPassword']);

    Route::prefix('/account_details/')->group(function () {
        Route::post('create', [AccountDetailsController::class, 'create']);
        Route::post('delete', [AccountDetailsController::class, 'delete']);
        Route::post('update', [AccountDetailsController::class, 'update']);
        Route::post('retrieveByParameter', [AccountDetailsController::class, 'retrieveByParameter']);

    });
    
    Route::post('addComment', [CommentsController::class, 'addComments']);
});

//testing
Route::get('comments', function() {
    return 'these are the comments';
});