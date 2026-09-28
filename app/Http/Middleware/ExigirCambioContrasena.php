<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Alias: cambio.contrasena
 * CU06: quien tiene una contraseña temporal debe reemplazarla antes de usar el sistema.
 */
class ExigirCambioContrasena
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->debe_cambiar_contrasena) {
            return redirect()->route('contrasena.cambiar')
                ->with('aviso', 'Debe reemplazar su contraseña temporal antes de continuar.');
        }

        return $next($request);
    }
}
