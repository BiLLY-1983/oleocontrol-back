<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Muestra una lista de todos los usuarios.
     * 
     * Este método recupera todos los usuarios de la base de datos y los devuelve
     * como una colección de recursos UserResource. La respuesta incluye un estado
     * de éxito y los datos de los usuarios en formato JSON.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con la lista de usuarios
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => UserResource::collection(User::all())
        ], 200);
    }

    /**
     * Crea un nuevo usuario.
     * 
     * Este método recibe una solicitud de creación de usuario, valida los datos y crea un nuevo usuario en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del usuario creado en formato JSON.
     *
     * @param StoreUserRequest $request La solicitud de creación de usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de éxito y los datos del usuario creado.
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
     * Muestra un usuario en concreto.
     * 
     * Este método recibe un ID de usuario, busca el usuario en la base de datos y devuelve los datos del usuario en formato JSON.
     * La respuesta incluye un estado de éxito y los datos del usuario en formato JSON.
     *
     * @param int $id El ID del usuario a mostrar.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de éxito y los datos del usuario.
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
     * Muestra el perfil del usuario autenticado.
     * 
     * Este método recupera el usuario autenticado y devuelve los datos del usuario en formato JSON.
     * La respuesta incluye un estado de éxito y los datos del usuario en formato JSON.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de éxito y los datos del usuario.
     */
    public function showProfile(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * Actualiza un usuario.
     * 
     * Este método recibe una solicitud de actualización de usuario, valida los datos y actualiza el usuario en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del usuario actualizado en formato JSON.
     *
     * @param UpdateUserRequest $request La solicitud de actualización de usuario.
     * @param int $id El ID del usuario a actualizar.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de éxito y los datos del usuario actualizado.
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
     * Actualiza el perfil de un usuario.
     * 
     * Este método recibe una solicitud de actualización de usuario, valida los datos y actualiza el usuario en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del usuario actualizado en formato JSON.
     *
     * @param UpdateUserRequest $request La solicitud de actualización de usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de éxito y los datos del usuario actualizado.
     */
    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $authUser = Auth::user();
        $user = User::findOrFail($authUser->id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $user->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * Elimina un usuario.
     * 
     * Este método recibe un ID de usuario, busca el usuario en la base de datos y elimina el usuario.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID del usuario a eliminar.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
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
