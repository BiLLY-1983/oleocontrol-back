<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMemberRoleMiddleware
{
    /**
     * Maneja una solicitud entrante y verifica si el usuario tiene el rol de Socio o Administrador.
     * 
     * Este método comprueba si el usuario autenticado tiene el rol de Socio o Administrador.
     * Si el usuario no está autenticado o no tiene ninguno de los roles requeridos, se aborta la
     * solicitud con un error 403 (Acceso denegado). Si el usuario tiene alguno de los roles
     * adecuados, se permite que la solicitud continúe su procesamiento normal.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP entrante
     * @param  \Closure  $next  La siguiente función middleware en la cadena
     * @return \Symfony\Component\HttpFoundation\Response  La respuesta HTTP resultante
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || (!$request->user()->hasRole('Socio') && !$request->user()->hasRole('Administrador'))) {
            abort(403, 'Acceso denegado. Se requiere rol de Socio o Administrador.');
        }

        return $next($request);
    }
}
