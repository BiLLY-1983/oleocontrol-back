<?php

use App\Http\Middleware\CheckRoleMiddleware;
use App\Http\Middleware\CheckAdminRoleMiddleware;
use App\Http\Middleware\CheckMemberRoleMiddleware;
use App\Http\Middleware\CheckEmployeeRoleMiddleware;
use App\Http\Middleware\CheckDepartmentMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRoleMiddleware::class,  
            'admin' => CheckAdminRoleMiddleware::class,
            'member' => CheckMemberRoleMiddleware::class,
            'employee' => CheckEmployeeRoleMiddleware::class,
            'department' => CheckDepartmentMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
