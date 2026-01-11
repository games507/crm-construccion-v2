<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Alias para Spatie Permissions (Laravel 11)
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,

            // TU MIDDLEWARE (el que daba error "Target class ... does not exist")
            'admin_or_superadmin' => \App\Http\Middleware\AdminOrSuperAdmin::class,
            'superadmin_only'     => \App\Http\Middleware\SuperAdminOnly::class,

        ]);

        // Middleware de contexto de empresa (sin Kernel)
        $middleware->web(append: [
            \App\Http\Middleware\EmpresaContext::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
