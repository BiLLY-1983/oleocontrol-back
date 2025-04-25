<?php

use App\Http\Controllers\api\OilSettlementController;
use Illuminate\Support\Facades\Route;

/* ================================= */
/* Rutas acceso tabla OilSettlements */
/* ================================= */

// Rutas para socios (y administradores)
Route::middleware('member')->group(function () {
    Route::get('/members/{memberId}/oil-settlements', [OilSettlementController::class, 'indexForMember']);
});
