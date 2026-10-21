<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige un permiso concreto para entrar en la ruta.
 *
 * Se usa así: `->middleware('permiso:articulos.editar')`.
 *
 * La comprobación ocurre en el backend, no en la interfaz: ocultar un botón
 * es una comodidad visual, no una protección. Este middleware es la
 * frontera real.
 */
class EnsureUserHasPermission
{
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->puede($permiso)) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }
}
