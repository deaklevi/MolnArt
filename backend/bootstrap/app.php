<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
<<<<<<< HEAD
        api: __DIR__.'/../routes/api.php',
=======
>>>>>>> 9c00869fac303a97c058f4480a66c501c93583d7
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
<<<<<<< HEAD
        // A statefulApi() mellé (vagy helyett) hozzáadjuk a kivételt:
        $middleware->statefulApi(); 

        $middleware->validateCsrfTokens(except: [
            'api/*', // Ez felszabadítja a vendég foglalást a CSRF kényszer alól
        ]);
=======
        //
>>>>>>> 9c00869fac303a97c058f4480a66c501c93583d7
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
