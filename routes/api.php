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
use App\Http\Controllers\api\EmployeeController;
use Illuminate\Support\Facades\Route;

/* Rutas para el Login/Logout */
/* -------------------------- */
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'role'])->group(function () {

    /* -------------------------- */
    /* Rutas para Administradores */
    /* -------------------------- */
    Route::apiResources([
        'users' => UserController::class,
        'roles' => RoleController::class,
        'departments' => DepartmentController::class,
        'employees' => EmployeeController::class,
        'members' => MemberController::class,
        'entries' => EntryController::class,
        'settlements' => SettlementController::class,
        'oils' => OilController::class,
        'analyses' => AnalysisController::class,
        'notifications' => NotificationController::class,
    ]);

    /* ----------------- */
    /* Rutas para Socios */
    /* ----------------- */
    Route::get('entries/{id}', [UserController::class, 'show']); 
    Route::put('users/{id}', [UserController::class, 'update']); 
    Route::patch('users/{id}', [UserController::class, 'update']); 

    Route::get('getEntriesUser/{member_id}', [EntryController::class, 'getEntriesUser']); 
    //Route::get('analyses/{id}', [AnalysisController::class]); 
    //Route::get('settlements/{id}', [SettlementController::class]); 

    /* -------------------- */
    /* Rutas para Empleados */
    /* -------------------- */
});



