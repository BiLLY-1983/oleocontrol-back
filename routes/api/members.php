<?php

use App\Http\Controllers\api\MemberController;
use Illuminate\Support\Facades\Route;

/* ========================== */
/* Rutas acceso tabla Members */
/* ========================== */

// Rutas para empleados del departamento de "Administración" y administradores
Route::middleware('department:Administración')->group(function () {
    Route::get('/members', [MemberController::class, 'index']);
    Route::post('/members', [MemberController::class, 'store']);
    Route::get('/members/{id}', [MemberController::class, 'show']);
    Route::put('/members/{id}', [MemberController::class, 'update']);
    Route::delete('/members/{id}', [MemberController::class, 'destroy']);
});

