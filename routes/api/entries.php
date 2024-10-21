<?php

use App\Http\Controllers\api\EntryController;
use Illuminate\Support\Facades\Route;

/* ========================== */
/* Rutas acceso tabla Entries */
/* ========================== */

// Rutas para administradores
Route::middleware('admin')->group(function () {
    Route::get('/entries', [EntryController::class, 'index']);
    Route::get('/entries/{entryId}', [EntryController::class, 'show']);
    Route::delete('/entries/{entryId}', [EntryController::class, 'destroy']);
});

// Rutas para empleados (y administradores)
Route::middleware('department:Control de entradas')->group(function () {
    Route::post('/entries', [EntryController::class, 'store']);
    Route::put('/entries/{entryId}', [EntryController::class, 'update']);
});

// Rutas para socios (y administradores)
Route::middleware('member')->group(function () {
    Route::get('/members/{memberId}/entries', [EntryController::class, 'indexForMember']);
    Route::get('/members/{memberId}/entries/{entryId}', [EntryController::class, 'showForMember']);
});
