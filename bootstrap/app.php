<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureExistSignature;
use App\Http\Middleware\EnsureArabicName;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'signature' => EnsureExistSignature::class,
            'arabicName' => EnsureArabicName::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
