<?php

use App\Http\Controllers\api\DepartmentController;
use Illuminate\Support\Facades\Route;

/* ============================== */
/* Rutas acceso tabla Departments */
/* ============================== */

// Rutas para administradores
Route::middleware('admin')->group(function () {
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::get('/departments/{id}', [DepartmentController::class, 'show']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);
});