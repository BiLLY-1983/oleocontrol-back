<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     *   Función que muestra los usuarios del sistema.
     * 
     *   @return JsonResponse Respuesta JSON con un mensaje de éxito
     *   y una colección con todos los usuarios de la aplicación.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => UserResource::collection(User::all())
        ], 200);
    }

    /**
    *   Función para agregar un nuevo usuario.
    *
    *   @param StoreUserRequest Solicitud validada con los datos del nuevo usuario.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos del usuario creado.
    */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
    *   Función para mostrar un usuario en concreto.
    *
    *   @param int ID del usuario a mostrar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos del usuario.
    */
    public function show($id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
    *   Función para actualizar un usuario.
    *
    *   @param UpdateUserRequest Solicitud validada con los datos del
    *   nuevo usuario.
    *   @param int ID del usuario a actualizar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los 
    *   datos del usuario.
    */
    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
    *   Función para eliminar un usuario.
    *
    *   @param int ID del usuario a eliminar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito.
    */
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario eliminado satisfactoriamente'
        ], 200);
    }
}
