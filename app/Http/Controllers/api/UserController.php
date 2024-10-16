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
     *   Función que muestra los usuarios del sistema.
     * 
     *   Verifica si el usuario es 'Administrador':
     *   -   Si lo es, retorna una coleción con todos los usuarios del sistema.
     *   -   Si no lo es, devuelve un mensaje de error.
     * 
     *   @return JsonResponse Respuesta JSON con un mensaje de éxito
     *   y una colección con todos los usuarios de la aplicación si el usuario es 'Administrador', 
     *   o un mensaje de error si no tiene permisos.
     */
    public function index(): JsonResponse
    {
        // Verificar si el usuario autenticado es un administrador
        if (!Auth::user()->roles->contains('name', 'Administrador')) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permisos para listar los usuarios.'
            ], 403);
        }

        // Respuesta del recurso
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
        // Verificar si el usuario autenticado es un administrador
        if (!Auth::user()->roles->contains('name', 'Administrador')) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permisos para listar los usuarios.'
            ], 403);
        }

        $user = User::create($request->validated());
        
        // Respuesta del recurso
        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ], 201);
    }

    /**
    *   Función para mostrar un usuario en concreto.
    *
    *   Verifica si el usuario es 'administrador':
    *   -   Si lo es, retorna el recurso.
    *   -   Si no lo es, se verifica si el usuario autenticado está intentando acceder a su propio perfil:
    *       · Si el ID del usuario autenticado no coincide con el ID proporcionado, 
    *           se devuelve un mensaje de error con un código 403 (Prohibido).
    *       · Si el ID coincide, se retorna un recurso del usuario.
    *
    *   @param int ID del usuario a mostrar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos del usuario, 
    *   o un mensaje de error si no tiene permisos.
    */
    public function show($id): JsonResponse
    {
        // Si el usuario es 'Administrador' puede ver cualquier perfil
        if (Auth::user()->roles->contains('name', 'Administrador')) {
            return response()->json([
                'status' => 'success',
                'data' => new UserResource(User::findOrFail($id))
            ], 200);
        }

        // Comprobación del id autenticado y el id recibido
        if (Auth::id() !== (int)$id) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permisos para ver este perfil.'
            ], 403);
        }

        // Respuesta del recurso
        return response()->json([
            'status' => 'success',
            'data' => new UserResource(User::findOrFail($id))
        ], 200);
    }

    /**
    *   Función para actualizar un usuario.
    *
    *   Verifica si el usuario autenticado está intentando acceder a su propio perfil:
    *   -   Si el ID del usuario autenticado no coincide con el ID proporcionado, 
    *           se devuelve un mensaje de error con un código 403 (Prohibido).
    *   -   Si el ID coincide, se permite la actualización del usuario.
    *
    *   @param UpdateUserRequest Solicitud validada con los datos del nuevo usuario.
    *   @param int ID del usuario a actualizar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos del usuario, 
    *   o un mensaje de error si no tiene permisos.
    */
    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Verificación: el usuario autenticado solo puede actualizar su propio perfil
        if (Auth::id() !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permisos para actualizar este perfil.'
            ], 403);
        }

        // Si el ID coincide, se permite la actualización
        $user->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ], 200);
    }

    /**
    *   Función para eliminar un usuario.
    *
    *   Verifica si el usuario es 'Administrador':
    *   -   Si lo es, elimina el usuario y devuelve un mensaje de éxito.
    *   -   Si no lo es, devuelve un mensaje de error.
    *
    *   @param int ID del usuario a eliminar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito, 
    *   o un mensaje de error
    */
    public function destroy($id): JsonResponse
    {
        // Verificar si el usuario autenticado es un administrador
        if (!Auth::user()->roles->contains('name', 'Administrador')) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permisos para listar los usuarios.'
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
