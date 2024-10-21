<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    /**
     * Muestra todos los departamentos.
     * 
     * Este método obtiene todos los departamentos de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los departamentos.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los departamentos.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => DepartmentResource::collection(Department::all())
        ], 200);
    }

    /**
     * Crea un nuevo departamento.
     * 
     * Este método recibe una solicitud de creación de departamento, valida los datos y crea un nuevo departamento en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del departamento creado en formato JSON.
     *
     * @param StoreDepartmentRequest $request La solicitud de creación de departamento.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del departamento creado.
     */
    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = Department::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new DepartmentResource($department)
        ], 201);
    }

    /**
     * Muestra un departamento específico por su ID.
     * 
     * Este método recibe un ID de departamento, busca el departamento en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del departamento.
     *
     * @param int $id El ID del departamento.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del departamento.
     */
    public function show($id): JsonResponse
    {
        $department = Department::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new DepartmentResource($department)
        ], 200);
    }

    /**
     * Actualiza un departamento específico por su ID.
     * 
     * Este método recibe una solicitud de actualización de departamento, valida los datos y actualiza el departamento en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del departamento actualizado en formato JSON.
     *
     * @param UpdateDepartmentRequest $request La solicitud de actualización de departamento.
     * @param int $id El ID del departamento.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del departamento actualizado.
     */
    public function update(UpdateDepartmentRequest $request, $id): JsonResponse
    {
        $department = Department::findOrFail($id);
        $department->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new DepartmentResource($department)
        ], 200);
    }

    /**
     * Elimina un departamento específico por su ID.
     * 
     * Este método recibe un ID de departamento, busca el departamento en la base de datos y elimina el departamento.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID del departamento.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     */
    public function destroy($id): JsonResponse
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Departamento eliminado satisfactoriamente'
        ], 200);
    }
}
