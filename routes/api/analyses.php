<?php

use App\Http\Controllers\api\AnalysisController;
use App\Http\Controllers\api\EntryController;
use Illuminate\Support\Facades\Route;

/* =========================== */
/* Rutas acceso tabla Analyses */
/* =========================== */

// Rutas para administradores
Route::middleware('admin')->group(function () {
    Route::get('/employees/{employeeId}/analyses', [AnalysisController::class, 'indexForEmployee']);
    Route::get('/employees/{employeeId}/analyses/{analysisId}', [AnalysisController::class, 'showForEmployee']);
    //Route::put('/employees/{employeeId}/analyses/{analysisId}', [AnalysisController::class, 'updateForEmployee']);
});

// Rutas para empleados que pertenecen al departamento de laboratorio (y administradores)
Route::middleware('department:Laboratorio')->group(function () {
    Route::get('/analyses', [AnalysisController::class, 'index']);
    Route::get('/analyses/{analysisId}', [AnalysisController::class, 'show']);
    Route::put('/analyses/{analysisId}', [AnalysisController::class, 'update']);
    Route::delete('/analyses/{analysisId}', [AnalysisController::class, 'destroy']);
});

// Rutas para socios (y administradores)
Route::middleware('member')->group(function () {
    Route::get('/members/{memberId}/analyses', [AnalysisController::class, 'indexForMember']);
    Route::get('/members/{memberId}/entries/{entryId}/analyses', [AnalysisController::class, 'showForMember']);
});
