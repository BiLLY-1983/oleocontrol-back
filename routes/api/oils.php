<?php

use App\Http\Controllers\api\OilController;
use Illuminate\Support\Facades\Route;

/* ======================= */
/* Rutas acceso tabla Oils */
/* ======================= */

// Rutas solo para administradores
Route::middleware('admin')->group(function () {
    Route::post('/oils', [OilController::class, 'store']);
    Route::put('/oils/{oilId}', [OilController::class, 'update']);
    Route::delete('/oils/{oilId}', [OilController::class, 'destroy']);
});

Route::get('/oils', [OilController::class, 'index']);
Route::get('/oils/{oilId}', [OilController::class, 'show']);

