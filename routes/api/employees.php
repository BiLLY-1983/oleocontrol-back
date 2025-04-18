<?php

use App\Http\Controllers\api\EmployeeController;
use Illuminate\Support\Facades\Route;

/* ============================ */
/* Rutas acceso tabla Employees */
/* ============================ */

// Rutas para empleados del departamento de Recursos Humanos (RRHH) y administradores
Route::middleware('department:RRHH')->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::get('/employeeByUser/{id}', [EmployeeController::class, 'indexByUser']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);
    Route::put('/employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);
});

