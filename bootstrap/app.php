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
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(MiddlewareName::class);
        $middleware->alias([
            'EscapeRequestInput' => \App\Http\Middleware\EscapeRequestInput::class,
            'PreventBackHistory' => \App\Http\Middleware\PreventBackHistory::class,
            'CheckUserToken' => \App\Http\Middleware\CheckUserToken::class,
            'auth.redirect' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'guest.redirect' => \App\Http\Middleware\RedirectIfNotAuthenticated::class,
            'DecodeModSecurityPlaceholders' => \App\Http\Middleware\DecodeModSecurityPlaceholders::class,
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
