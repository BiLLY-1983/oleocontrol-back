<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Verificación del rol del usuario.
     * 
     * - Administradores tienen acceso total.
     * - Socios tiene acceso a ver y editar sus datos.
     * - Empleados tienen acceso a ver y editar sus datos.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json(['message' => 'Necesitas estar autenticado'], 403);
        }

        // Verificar si es 'Administrador'. Si lo es se permite el acceso.
        if ($user->roles->contains('name', 'Administrador')) {
            return $next($request);
        }
           
        // Verificar si es 'Socio'.
        if ($user->roles->contains('name', 'Socio')) {
            // Permitir solicitudes GET
            if ($request->isMethod('get') && $request->route('user') == $user->id) {
                return $next($request);
            }

            // Permitir PUT o PATCH solo en la ruta del propio usuario
            if (($request->isMethod('put') || $request->isMethod('patch')) &&
                $request->route('user') == $user->id) {
                return $next($request);
            }

            // Si no cumple con las condiciones, denegar el acceso
            return response()->json(['message' => 'No tienes acceso a este recurso'], 403);
        }

        // Verificar si es 'Empleado'.
        if ($user->roles->contains('name', 'Empleado')) {
            // Permitir solicitudes GET
            if ($request->isMethod('get') && $request->route('user') == $user->id) {
                return $next($request);
            }

            // Permitir PUT o PATCH solo en la ruta del propio usuario
            if (($request->isMethod('put') || $request->isMethod('patch')) &&
                $request->route('user') == $user->id) {
                return $next($request);
            }

            // Si no cumple con las condiciones, denegar el acceso
            return response()->json(['message' => 'No tienes acceso a este recurso'], 403);
        }

        // Si no tiene ninguno de los roles, denegar el acceso
        return response()->json(['message' => __('http-statuses.403')], 403);
    }
}
