<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class EmployeeController extends Controller
{
    /**
     * Muestra todos los empleados.
     * 
     * Este método obtiene todos los empleados de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los empleados.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los empleados.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => EmployeeResource::collection(Employee::all())
        ], 200);
    }

    /**
     * Crea un nuevo empleado.
     * 
     * Este método recibe una solicitud de creación de empleado, valida los datos y crea un nuevo empleado en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del empleado creado en formato JSON.
     *
     * @param StoreEmployeeRequest $request La solicitud de creación de empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado creado.
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 201);
    }

    /**
     * Muestra un empleado específico por su ID.
     * 
     * Este método recibe un ID de empleado, busca el empleado en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del empleado.
     *
     * @param int $id El ID del empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado.
     */
    public function show($id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 200);
    }

    /**
     * Actualiza un empleado específico por su ID.
     * 
     * Este método recibe una solicitud de actualización de empleado, valida los datos y actualiza el empleado en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del empleado actualizado en formato JSON.
     *
     * @param UpdateEmployeeRequest $request La solicitud de actualización de empleado.
     * @param int $id El ID del empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado actualizado.
     */
    public function update(UpdateEmployeeRequest $request, $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 200);
    }

    /**
     * Elimina un empleado específico por su ID.
     * 
     * Este método recibe un ID de empleado, busca el empleado en la base de datos y elimina el empleado.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID del empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     */
    public function destroy($id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'status' => 'success',
        ], 204);
    }

    /**
     * Muestra el perfil del empleado autenticado.
     * 
     * Este método obtiene el empleado autenticado y devuelve una respuesta JSON con un estado de éxito y los datos del empleado.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado.
     */
    public function showProfile(): JsonResponse
    {
        $employee = Auth::user()->employee;

        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 200);
    }

    /**
     * Actualiza el perfil del empleado autenticado.
     * 
     * Este método recibe una solicitud de actualización de empleado, valida los datos y actualiza el empleado en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del empleado actualizado en formato JSON.
     *
     * @param UpdateEmployeeRequest $request La solicitud de actualización de empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado actualizado.
     */
    public function updateProfile(UpdateEmployeeRequest $request): JsonResponse
    {
        $employee = Auth::user()->employee;
        $employee->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 200);
    }
}
