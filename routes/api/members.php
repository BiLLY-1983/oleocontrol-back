<?php

use App\Http\Controllers\api\MemberController;
use Illuminate\Support\Facades\Route;

/* ========================== */
/* Rutas acceso tabla Members */
/* ========================== */

// Rutas para administradores
Route::get('/members', [MemberController::class, 'index']);
Route::post('/members', [MemberController::class, 'store']);
Route::get('/members/{id}', [MemberController::class, 'show']);
Route::put('/members/{id}', [MemberController::class, 'update']);
Route::delete('/members/{id}', [MemberController::class, 'destroy']);

// Rutas para socios
Route::get('/members/profile', [MemberController::class, 'showProfile']);
Route::put('/members/profile', [MemberController::class, 'updateProfile']);
