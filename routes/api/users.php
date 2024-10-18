<?php

use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;

/* ======================== */
/* Rutas acceso tabla Users */
/* ======================== */

// Rutas para administradores
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Rutas para socios y empleados
Route::get('/users/profile', [UserController::class, 'showProfile']);
Route::put('/users/profile', [UserController::class, 'updateProfile']);

