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


Route::get('/', function () {
    return view('index');
});
Route::prefix('/app/')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->name('app.login');
    Route::post('forgot_password', [AuthController::class, 'forgot_password']);
    Route::post('reset_password', [AuthController::class, 'reset_password']);
    
    

    Route::prefix('/authenticate/')->group(function () {
        Route::post('auth', [AuthController::class, 'authenticate']);
        Route::post('user', [AuthController::class, 'getAuthenticatedUser']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('invalidate', [AuthController::class, 'deauthenticate']);
    });

    Route::prefix('/comments/')->group(function () {
        Route::post('create', [CommentsController::class, 'create']);
        Route::post('createViaApplication', [CommentsController::class, 'createViaApplication']);
        Route::post('retrieveOne', [CommentsController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultiple', [CommentsController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveAll', [CommentsController::class, 'retrieveAll']);
        Route::post('retrieveWithAccountDetails', [CommentsController::class, 'retrieveWithAccountDetails']);
        Route::post('update', [CommentsController::class, 'update']);
        Route::post('delete', [CommentsController::class, 'delete']);
    });

    Route::prefix('/user/')->group(function () {
        Route::post('retrieveAll', [UserController::class, 'retrieveAll']);
        Route::post('retrieveOne', [UserController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultiple', [UserController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveWithAccountDetails', [UserController::class, 'retrieveWithAccountDetailsWithEmail']);
        Route::post('retrieveEmailAccountDetails', [UserController::class, 'retrieveEmailAccountDetails']);
        Route::post('retrieveFilter', [UserController::class, 'retrieveMultipleByFilter']);
        Route::post('retrieveStatistics', [UserController::class, 'retrieveStatistics']);
        Route::post('delete', [UserController::class, 'delete']);
        Route::post('update', [UserController::class, 'update']);
        Route::post('updateProfile', [UserController::class, 'updateProfile']);
        Route::post('updateEmail', [UserController::class, 'updateEmail']);
        Route::post('updatePassword', [UserController::class, 'updatePassword']);
        Route::post('paginate', [UserController::class, 'paginate']);
    });
    // 2-20-24 update statements are not yet done, new update method
    // 3-19-24 update statements routed
    Route::prefix('/account_details/')->group(function () {
        Route::post('create', [AccountDetailsController::class, 'create']);
        Route::post('delete', [AccountDetailsController::class, 'delete']);
        
        Route::post('update', [AccountDetailsController::class, 'updateByParameter']);
        Route::post('setupProfile', [AccountDetailsController::class, 'setupProfile']);
        Route::post('uploadNewFiles', [AccountDetailsController::class, 'uploadNewFiles']);
        Route::post('updateDataAndFiles', [AccountDetailsController::class, 'updateDataAndFiles']);
        Route::post('retrieveOne', [AccountDetailsController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultipleByParameter', [AccountDetailsController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveAll', [AccountDetailsController::class, 'retrieveAll']);
    });

    Route::prefix('/leave_application/')->group(function () {
        Route::post('create', [LeaveApplicationController::class, 'create']);
        Route::post('retrieveOne', [LeaveApplicationController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultipleByParameter', [LeaveApplicationController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveAll', [LeaveApplicationController::class, 'retrieveAll']);
        Route::post('update', [LeaveApplicationController::class, 'update']);
        Route::post('updateOne', [LeaveApplicationController::class, 'updateOne']);
        Route::post('delete', [LeaveApplicationController::class, 'delete']);
    });

    Route::prefix('/admin_system_message/')->group(function () {
        Route::post('create', [AdminSystemMessageController::class, 'create']);
        Route::post('retrieveOne', [AdminSystemMessageController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultipleByParameter', [AdminSystemMessageController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveAll', [AdminSystemMessageController::class, 'retrieveAll']);
        Route::post('retrieveViaDashboard', [AdminSystemMessageController::class, 'retrieveViaDashboard']);
        Route::post('paginate', [AdminSystemMessageController::class, 'paginate']);

        Route::post('update', [AdminSystemMessageController::class, 'update']);
        Route::post('delete', [AdminSystemMessageController::class, 'delete']);
    });
    Route::prefix('/scholar/')->group(function () {
        Route::post('create', [ScholarController::class, 'create']);
        Route::post('retrieveOneByParameter', [ScholarController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultipleByParameter', [ScholarController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveAll', [ScholarController::class, 'retrieveAll']);
        Route::post('filter', [ScholarController::class, 'filterRetrieve']);
        Route::post('update', [ScholarController::class, 'update']);
        Route::post('delete', [ScholarController::class, 'delete']);
    });
    Route::prefix('/scholar_portfolio/')->group(function () {
        Route::post('create', [ScholarPortfolioController::class, 'create']);
        Route::post('retrieveOneByParameter', [ScholarPortfolioController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultipleByParameter', [ScholarPortfolioController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveByEmail', [ScholarPortfolioController::class, 'retrieveByEmail']);
        Route::post('retrieveAll', [ScholarPortfolioController::class, 'retrieveAll']);
        Route::post('update', [ScholarPortfolioController::class, 'update']);
        Route::post('updateOne', [ScholarPortfolioController::class, 'updateOne']);
        Route::post('delete', [ScholarPortfolioController::class, 'delete']);
    });
    Route::prefix('/scholar_request/')->group(function () {
        Route::post('create', [ScholarRequestApplicationController::class, 'create']);
        Route::post('retrieveOne', [ScholarRequestApplicationController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultipleByParameter', [ScholarRequestApplicationController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveAll', [ScholarRequestApplicationController::class, 'retrieveAll']);
        Route::post('retrievePendingTableAndDetail', [ScholarRequestApplicationController::class, 'retrievePendingTableAndDetail']);
        Route::post('retrieveEndorsedTableAndDetail', [ScholarRequestApplicationController::class, 'retrieveEndorsedTableAndDetail']);
        Route::post('retrieveUserApplications', [ScholarRequestApplicationController::class, 'retrieveUserApplications']);
        Route::post('approveApplicant', [ScholarRequestApplicationController::class, 'approveApplicant']);
        Route::post('paginate', [ScholarRequestApplicationController::class, 'paginate']);
        Route::post('paginateEndorsed', [ScholarRequestApplicationController::class, 'paginateEndorsed']);

        Route::post('update', [ScholarRequestApplicationController::class, 'update']);
        Route::post('updateToEndorsed', [ScholarRequestApplicationController::class, 'updateToEndorsed']);
        Route::post('delete', [ScholarRequestApplicationController::class, 'delete']);
        Route::post('reject', [ScholarRequestApplicationController::class, 'reject']);
    });
    Route::prefix('/scholar_tasks/')->group(function () {
        Route::post('create', [ScholarTasksController::class, 'create']);
        Route::post('retrieveOne', [ScholarTasksController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultipleByParameter', [ScholarTasksController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveAll', [ScholarTasksController::class, 'retrieveAll']);
        Route::post('updateOne', [ScholarTasksController::class, 'updateOne']);
        Route::post('update', [ScholarTasksController::class, 'update']);
        Route::post('delete', [ScholarTasksController::class, 'delete']);
    });
    
    
    Route::prefix('/notification/')->group(function () {
        Route::post('create', [NotificationController::class, 'create']);
        Route::post('retrieveOne', [NotificationController::class, 'retrieveOneByParameter']);
        Route::post('retrieveMultiple', [NotificationController::class, 'retrieveMultipleByParameter']);
        Route::post('retrieveAll', [NotificationController::class, 'retrieveAll']);
        Route::post('update', [NotificationController::class, 'update']);
        Route::post('delete', [NotificationController::class, 'delete']);
    });
});
