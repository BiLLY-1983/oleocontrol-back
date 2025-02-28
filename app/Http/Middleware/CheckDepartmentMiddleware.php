<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartmentMiddleware
{
    
    /**
     * Maneja una solicitud entrante y verifica si el usuario tiene acceso al departamento especificado.
     *
     * Este método realiza las siguientes comprobaciones:
     * 1. Verifica si el usuario está autenticado.
     * 2. Si el usuario es administrador, permite el acceso sin verificar el departamento.
     * 3. Verifica si el usuario tiene el rol de 'Empleado'.
     * 4. Comprueba si el empleado pertenece a uno de los departamentos especificados.
     *
     * @param  \Illuminate\Http\Request  $request  La solicitud HTTP entrante
     * @param  \Closure  $next  La siguiente función middleware en la cadena
     * @param  string  ...$departments  Los nombres de los departamentos permitidos
     * @return \Symfony\Component\HttpFoundation\Response  La respuesta HTTP resultante
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException  Si el acceso es denegado
     */
    public function handle(Request $request, Closure $next, ...$departments): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Acceso denegado. Usuario no autenticado.');
        }

        // Si el usuario es administrador, permitir acceso sin verificar el departamento
        if ($user->hasRole('Administrador')) {
            return $next($request);
        }

        if (!$user->hasRole('Empleado')) {
            abort(403, 'Acceso denegado. Se requiere rol de Empleado.');
        }

        $employee = $user->employee;

        if (!$employee || !in_array($employee->department->name, $departments)) {
            abort(403, 'Acceso denegado. No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
