<?php

use App\Http\Controllers\api\EmployeeController;
use Illuminate\Support\Facades\Route;

/* ============================ */
/* Rutas acceso tabla Employees */
/* ============================ */

// Rutas para administradores
Route::get('/employees', [EmployeeController::class, 'index']);
Route::post('/employees', [EmployeeController::class, 'store']);
Route::get('/employees/{id}', [EmployeeController::class, 'show']);
Route::put('/employees/{id}', [EmployeeController::class, 'update']);
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

// Rutas para empleados (y administradores)
Route::get('/employees/profile', [EmployeeController::class, 'showProfile']);
Route::put('/employees/profile', [EmployeeController::class, 'updateProfile']);
