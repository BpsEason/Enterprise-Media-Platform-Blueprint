<?php

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
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        if ($dsn = env('SENTRY_DSN')) {
            \Sentry\Laravel\Integration::sendPii(true);
            \Sentry\Laravel\Integration::captureAllExceptions();
            \Sentry\Laravel\Integration::captureUnhandledRejections();
            \Sentry\Laravel\Integration::captureDeprecated();
        }
    })->create();
