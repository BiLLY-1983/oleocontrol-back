<?php

use App\Http\Controllers\api\RoleController;
use Illuminate\Support\Facades\Route;

/* ======================== */
/* Rutas acceso tabla Roles */
/* ======================== */
    
// Rutas para administradores
Route::get('/roles', [RoleController::class, 'index']);
Route::post('/roles', [RoleController::class, 'store']);
Route::get('/roles/{id}', [RoleController::class, 'show']);
Route::put('/roles/{id}', [RoleController::class, 'update']);
Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
