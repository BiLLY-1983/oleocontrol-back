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

// Ruta común para todos los usuarios autenticados (acceder y editar su propio perfil)
Route::get('/profile', [UserController::class, 'showProfile']);
Route::put('/profile', [UserController::class, 'updateProfile']);
