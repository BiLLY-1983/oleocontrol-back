<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Muestra todos los roles.
     * 
     * Este método obtiene todos los roles de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los roles.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los roles.
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Obtener todos los roles",
     *     description="Devuelve una lista con todos los roles registrados.",
     *     operationId="getRoles",
     *     tags={"Roles"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de roles",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/RoleResource")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => RoleResource::collection(Role::all())
        ], 200);
    }

    /**
     * Crea un nuevo rol.
     * 
     * Este método recibe una solicitud de creación de rol, valida los datos y crea un nuevo rol en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del rol creado en formato JSON.
     *
     * @param StoreRoleRequest $request La solicitud de creación de rol.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del rol creado.
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Crear un nuevo rol",
     *     description="Crea un nuevo rol en el sistema.",
     *     operationId="storeRole",
     *     tags={"Roles"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Operario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rol creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/RoleResource")
     *         )
     *     )
     * )
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new RoleResource($role)
        ], 201);
    }

    /**
     * Muestra un rol específico por su ID.
     * 
     * Este método recibe un ID de rol, busca el rol en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del rol.
     *
     * @param int $id El ID del rol.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del rol.
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="Obtener un rol específico",
     *     description="Devuelve los detalles de un rol por su ID.",
     *     operationId="showRole",
     *     tags={"Roles"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/RoleResource")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $role = Role::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new RoleResource($role)
        ], 200);
    }

    /**
     * Actualiza un rol específico por su ID.
     * 
     * Este método recibe una solicitud de actualización de rol, valida los datos y actualiza el rol en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del rol actualizado en formato JSON.
     *
     * @param UpdateRoleRequest $request La solicitud de actualización de rol.
     * @param int $id El ID del rol.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del rol actualizado.
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Actualizar un rol",
     *     description="Actualiza los datos de un rol existente.",
     *     operationId="updateRole",
     *     tags={"Roles"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Responsable")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/RoleResource")
     *         )
     *     )
     * )
     */
    public function update(UpdateRoleRequest $request, $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new RoleResource($role)
        ], 200);
    }

    /**
     * Elimina un rol específico por su ID.
     * 
     * Este método recibe un ID de rol, busca el rol en la base de datos y elimina el rol.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID del rol.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     summary="Eliminar un rol",
     *     description="Elimina un rol existente por su ID.",
     *     operationId="deleteRole",
     *     tags={"Roles"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del rol a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rol eliminado satisfactoriamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Rol eliminado satisfactoriamente")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Rol eliminado satisfactoriamente'
        ], 200);
    }
}
