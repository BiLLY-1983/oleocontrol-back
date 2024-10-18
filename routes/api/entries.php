<?php

use App\Http\Controllers\api\EntryController;
use Illuminate\Support\Facades\Route;

/* ========================== */
/* Rutas acceso tabla Entries */
/* ========================== */

// Rutas para administradores
Route::get('/entries', [EntryController::class, 'index']);
Route::delete('/entries/{entryId}', [EntryController::class, 'destroy']);

// Rutas para empleados (y administradores)
Route::post('/entries', [EntryController::class, 'store']);
Route::put('/entries/{entryId}', [EntryController::class, 'update']);

// Rutas para socios (y administradores)
Route::get('/members/{memberId}/entries', [EntryController::class, 'indexForMember']);
Route::get('/members/{memberId}/entries/{entryId}', [EntryController::class, 'showForMember']);
