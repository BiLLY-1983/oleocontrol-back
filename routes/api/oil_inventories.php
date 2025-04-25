<?php

use App\Http\Controllers\api\OilInventoryController;
use Illuminate\Support\Facades\Route;

/* ================================= */
/* Rutas acceso tabla OilInventories */
/* ================================= */

// Rutas para socios (y administradores)
Route::middleware('member')->group(function () {
    Route::get('/members/{memberId}/oil-inventories', [OilInventoryController::class, 'indexForMember']);
});
