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
