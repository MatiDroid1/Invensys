<?php

use App\Http\Middleware\EnsureOrganizacionActiva;
use App\Http\Middleware\EnsureUserHasPermission;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
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
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'permiso' => EnsureUserHasPermission::class,
            'organizacion' => EnsureOrganizacionActiva::class,
        ]);

        // Se comprueba `activo` en cada request, no solo al iniciar sesión.
        $middleware->web(append: [
            EnsureUserIsActive::class,
            EnsureOrganizacionActiva::class,
        ]);

        // Necesario solo si hay un proxy o balanceador delante. Confiar en
        // todos por defecto sería peligroso: cualquiera podría mandar
        // X-Forwarded-For falso y esquivar el límite de intentos de login,
        // así que se activa a propósito con TRUSTED_PROXIES.
        $proxies = env('TRUSTED_PROXIES');

        if ($proxies) {
            $middleware->trustProxies(at: $proxies);
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
