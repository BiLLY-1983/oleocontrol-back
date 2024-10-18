<?php

use App\Http\Controllers\api\NotificationController;
use Illuminate\Support\Facades\Route;

/* ================================ */
/* Rutas acceso tabla Notifications */
/* ================================ */

Route::get('/users/{userId}/sent-notifications',  [NotificationController::class, 'indexSent']);
Route::get('/users/{userId}/received-notifications', [NotificationController::class, 'indexReceived']);

Route::get('/users/{userId}/notifications/{notificationId}', [NotificationController::class, 'show']);

Route::post('/notifications', [NotificationController::class, 'store']);
