<?php

use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\HasPermission;
use App\Http\Middleware\HasRole;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\ShareSiteSettings;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            ShareSiteSettings::class,
        ]);

        $middleware->alias([
            'guest.only'  => RedirectIfAuthenticated::class,
            'role'        => HasRole::class,
            'can.do'      => HasPermission::class,
            'active'      => EnsureAccountIsActive::class,
            'verified.by' => EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
