<?php

use App\Http\Controllers\api\SettlementController;
use Illuminate\Support\Facades\Route;

/* ============================== */
/* Rutas acceso tabla Settlements */
/* ============================== */

// Rutas para administradores
Route::middleware('admin')->group(function () {
    Route::get('/employees/{employeeId}/settlements', [SettlementController::class, 'indexForEmployee']);
    Route::get('/employees/{employeeId}/settlements/{settlementId}', [SettlementController::class, 'showForEmployee']);
    //Route::put('/employees/{employeeId}/settlements/{settlementId}', [SettlementController::class, 'updateForEmployee']);   
});

// Rutas para empleados (y administradores)
Route::middleware('department:Contabilidad')->group(function () {
    Route::get('/settlements', [SettlementController::class, 'index']);
    Route::get('/settlements/{settlementId}', [SettlementController::class, 'show']);
    Route::put('/settlements/{settlementId}', [SettlementController::class, 'update']);
    Route::delete('/settlements/{settlementId}', [SettlementController::class, 'destroy']);
});

// Rutas para socios (y administradores)
Route::middleware('member')->group(function () {
    Route::get('/members/{memberId}/settlements', [SettlementController::class, 'indexForMember']);
    Route::get('/members/{memberId}/settlements/{settlementId}', [SettlementController::class, 'showForMember']);
    Route::post('/settlements', [SettlementController::class, 'store']);
});
