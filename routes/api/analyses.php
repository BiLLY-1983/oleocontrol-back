<?php

use App\Http\Controllers\api\AnalysisController;
use Illuminate\Support\Facades\Route;

/* =========================== */
/* Rutas acceso tabla Analyses */
/* =========================== */

// Rutas para administradores
Route::get('/analyses', [AnalysisController::class, 'index']);
Route::delete('/analyses/{analysisId}', [AnalysisController::class, 'destroy']);

// Rutas para empleados (y administradores)
Route::get('/employees/{employeeId}/analyses', [AnalysisController::class, 'indexForEmployee']);
Route::get('/employees/{employeeId}/analyses/{analysisId}', [AnalysisController::class, 'show']);
Route::put('/employees/{employeeId}/analyses/{analysisId}', [AnalysisController::class, 'update']);

// Rutas para miembros (y administradores)
Route::get('/members/{memberId}/entries/analyses', [AnalysisController::class, 'indexForMember']);
Route::get('/members/{memberId}/entries/{entryId}/analyses', [AnalysisController::class, 'showForMember']);