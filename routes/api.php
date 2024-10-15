<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\AnalysisController;
use App\Http\Controllers\api\DepartmentController;
use App\Http\Controllers\api\EntryController;
use App\Http\Controllers\api\MemberController;
use App\Http\Controllers\api\NotificationController;
use App\Http\Controllers\api\OilController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\SettlementController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\WorkerController;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('workers', WorkerController::class);
    Route::apiResource('members', MemberController::class);
    Route::apiResource('entries', EntryController::class);
    Route::apiResource('settlements', SettlementController::class);
    Route::apiResource('oils', OilController::class);
    Route::apiResource('analyses', AnalysisController::class);
    Route::apiResource('notifications', NotificationController::class);
});

/* 
Route::apiResource('users', UserController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('departments', DepartmentController::class);
Route::apiResource('workers', WorkerController::class);
Route::apiResource('members', MemberController::class);
Route::apiResource('entries', EntryController::class);
Route::apiResource('settlements', SettlementController::class);
Route::apiResource('oils', OilController::class);
Route::apiResource('analyses', AnalysisController::class);
Route::apiResource('notifications', NotificationController::class);
 */