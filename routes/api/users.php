<?php

use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;

/* ======================== */
/* Rutas acceso tabla Users */
/* ======================== */

// Rutas para administradores
Route::middleware('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// Rutas para socios (y administradores)
Route::middleware('member')->group(function () {
    Route::get('/members/profile', [UserController::class, 'showMemberProfile']);
    Route::put('/members/profile', [UserController::class, 'updateMemberProfile']);
});

// Rutas para empleados (y administradores)
Route::middleware('employee')->group(function () {
    Route::get('/employees/profile', [UserController::class, 'showEmployeeProfile']);
    Route::put('/employees/profile', [UserController::class, 'updateEmployeeProfile']);
});

