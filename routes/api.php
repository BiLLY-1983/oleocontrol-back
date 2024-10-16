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
        'workers' => WorkerController::class,
        'members' => MemberController::class,
        'entries' => EntryController::class,
        'settlements' => SettlementController::class,
        'oils' => OilController::class,
        'analyses' => AnalysisController::class,
        'notifications' => NotificationController::class,
    ]);

    /* ----------------- */
    /* Rutas para socios */
    /* ----------------- */
    Route::get('users/{user}', [UserController::class, 'show']); // Visualización de datos de usuario
    Route::put('users/{user}', [UserController::class, 'update']); // Edición de datos de usuario (propios)
    Route::patch('users/{user}', [UserController::class, 'update']); // Edición de datos de usuario (propios)
    
    //Route::get('entries', EntryController::class); // Visualización de entradas propias
    //Route::get('analyses', AnalysisController::class); // Visualización de análisis propios
    //Route::get('settlements', SettlementController::class); // Visualización de liquidaciones propias
});



