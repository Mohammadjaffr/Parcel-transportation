<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckAppIsActive;
use App\Http\Middleware\DriverMiddleware;
use App\Http\Middleware\SanctumApiAuthMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\UserMiddleware;
use App\Http\Middleware\VerifyAppAccessMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\CheckDevice::class);
        $middleware->web(append: [
            \App\Http\Middleware\MarkNotificationAsRead::class,
        ]); 
        $middleware->alias([
            "user"=>  UserMiddleware::class,
            "driver" => DriverMiddleware::class,
            "auth.sanctum.api" => SanctumApiAuthMiddleware::class,
            "verify.app.access" => VerifyAppAccessMiddleware::class,
            "super.admin" => SuperAdminMiddleware::class,
            "admin" => AdminMiddleware::class,
            'app.active' => CheckAppIsActive::class,
        ]);
        $middleware->appendToGroup('api', [
            'verify.app.access', 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
