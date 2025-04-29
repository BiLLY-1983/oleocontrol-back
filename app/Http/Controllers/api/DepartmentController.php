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
     * @OA\Get(
     *     path="/api/departments",
     *     summary="Obtener todos los departamentos",
     *     description="Devuelve una lista de todos los departamentos registrados en el sistema.",
     *     operationId="getDepartments",
     *     tags={"Departamentos"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de departamentos",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/DepartmentResource")
     *             )
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/departments",
     *     summary="Crear un nuevo departamento",
     *     description="Crea un nuevo departamento con los datos proporcionados.",
     *     operationId="storeDepartment",
     *     tags={"Departamentos"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Laboratorio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Departamento creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/DepartmentResource")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/departments/{id}",
     *     summary="Mostrar un departamento específico",
     *     description="Devuelve los detalles de un departamento dado su ID.",
     *     operationId="showDepartment",
     *     tags={"Departamentos"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del departamento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Departamento encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/DepartmentResource")
     *         )
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/departments/{id}",
     *     summary="Actualizar un departamento",
     *     description="Actualiza los datos de un departamento existente.",
     *     operationId="updateDepartment",
     *     tags={"Departamentos"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del departamento a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Recepción")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Departamento actualizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/DepartmentResource")
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/departments/{id}",
     *     summary="Eliminar un departamento",
     *     description="Elimina un departamento según su ID.",
     *     operationId="deleteDepartment",
     *     tags={"Departamentos"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del departamento a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Departamento eliminado satisfactoriamente"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Departamento eliminado satisfactoriamente'
        ], 204);
    }
}
