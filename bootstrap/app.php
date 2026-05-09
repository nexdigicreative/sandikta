<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'force.password.change' => \App\Http\Middleware\ForcePasswordChange::class,
            'check.active' => \App\Http\Middleware\CheckActiveUser::class,
            'auto.logout' => \App\Http\Middleware\AutoLogoutIdle::class,
        ]);

        $middleware->append(\App\Http\Middleware\SecureHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
