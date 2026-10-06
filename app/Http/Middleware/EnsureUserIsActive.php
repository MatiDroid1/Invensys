<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cierra la sesión de quien fue desactivado mientras estaba adentro.
 *
 * La columna `activo` solo se comprobaba al iniciar sesión. Eso dejaba un
 * hueco real: si un administrador desactiva a alguien, esa persona seguía
 * operando con la sesión que ya tenía abierta, incluso desde otra
 * computadora. Revisar en cada request cierra el hueco.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        // Se compara contra false y no con un "truthy" genérico a propósito:
        // si algún día la columna llega en null por una anomalía de datos,
        // expulsar a todo el mundo sería peor que dejar pasar a ese usuario.
        // La columna es NOT NULL en MySQL, así que la comparación estricta es
        // segura en producción.
        if ($usuario && $usuario->activo === false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Tu cuenta fue desactivada. Comunícate con un administrador.',
                ]);
        }

        return $next($request);
    }
}
