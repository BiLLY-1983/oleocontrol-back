<?php

use App\Http\Controllers\api\OilController;
use Illuminate\Support\Facades\Route;

/* ======================= */
/* Rutas acceso tabla Oils */
/* ======================= */

// Rutas solo para administradores
Route::post('/oils', [OilController::class, 'store']);
Route::put('/oils/{oilId}', [OilController::class, 'update']);
Route::delete('/oils/{oilId}', [OilController::class, 'destroy']);

// Rutas para todos los usuarios
Route::get('/oils', [OilController::class, 'index']);
Route::get('/oils/{oilId}', [OilController::class, 'show']);
