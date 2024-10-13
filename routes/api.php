<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OilController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

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
