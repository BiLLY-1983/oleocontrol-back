<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;

/* ========================== */
/* Rutas para el Login/Logout */
/* ========================== */
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    require __DIR__.'/api/users.php';
    require __DIR__.'/api/roles.php';
    require __DIR__.'/api/departments.php';
    require __DIR__.'/api/members.php';
    require __DIR__.'/api/employees.php';
    require __DIR__.'/api/entries.php';
    require __DIR__.'/api/analyses.php';
    require __DIR__.'/api/settlements.php';
    require __DIR__.'/api/oils.php';
    require __DIR__.'/api/notifications.php';
});

Route::post('/reset-password-request', [UserController::class, 'resetPasswordRequest']);

