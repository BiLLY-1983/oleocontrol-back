<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Http\Resources\UserResource;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    /**
     * Muestra todos los socios.
     * 
     * Este método obtiene todos los socios de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los socios.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los socios.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => MemberResource::collection(Member::all())
        ], 200);
    }

    /**
     * Crea un nuevo socio.
     * 
     * Este método recibe una solicitud de creación de socio, valida los datos y crea un nuevo socio en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del socio creado en formato JSON.
     *
     * @param StoreMemberRequest $request La solicitud de creación de socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio creado.
     */
    public function storeOld(StoreMemberRequest $request): JsonResponse
    {
        $member = Member::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 201);
    }

    public function store(StoreMemberRequest $request): JsonResponse
    {
        DB::beginTransaction(); // Iniciar la transacción

        try {
            // Crear el usuario
            $user = User::create($request->only([
                'username',
                'first_name',
                'last_name',
                'dni',
                'email',
                'password',
                'phone',
            ]));

            // Asignar rol al usuario
            $roleName = 'Socio';

            // Buscar el rol en la base de datos
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                throw new \Exception("El rol '{$roleName}' no existe.");
            }

            // Asociar el rol al usuario
            $user->roles()->attach($role->id);

            // Crear el socio
            $member = Member::create([
                'user_id' => $user->id,
                'member_number' => 1000 + $user->id,
            ]);

            // Verificar si el socio se creó correctamente
            if (!$member) {
                throw new \Exception('No se pudo crear el socio.');
            }

            DB::commit(); // Confirmar la transacción

            return response()->json([
                'status' => 'success',
                'data' => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Hacer rollback si ocurre un error

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra un socio específico por su ID.
     * 
     * Este método recibe un ID de socio, busca el socio en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del socio.
     *
     * @param int $id El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio.
     */
    public function show($id): JsonResponse
    {
        $member = Member::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 200);
    }

    /**
     * Actualiza un socio específico por su ID.
     * 
     * Este método recibe una solicitud de actualización de socio, valida los datos y actualiza el socio en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del socio actualizado en formato JSON.
     *
     * @param UpdateMemberRequest $request La solicitud de actualización de socio.
     * @param int $id El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio actualizado.
     */
    public function updateOld(UpdateMemberRequest $request, $id): JsonResponse
    {
        $member = Member::findOrFail($id);
        $member->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 200);
    }

    public function update(UpdateMemberRequest $request, $id): JsonResponse
    {
        DB::beginTransaction(); // Inicia la transacción

        try {
            // Buscar el miembro por su ID
            $member = Member::findOrFail($id);

            // Actualizar los datos del miembro
            $member->update($request->only(['member_number', 'status']));

            // Obtener los datos del usuario del request
            $userData = $request->input('user');
            $user = $member->user; // Obtener el usuario asociado al miembro

            // Actualizar los campos del usuario
            if ($user) {
                $user->update([
                    'username' => $userData['username'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'dni' => $userData['dni'],
                    'email' => $userData['email'],
                    'phone' => $userData['phone'],
                    'status' => $userData['status']
                ]);
            }

            // Si todo ha ido bien, confirmar la transacción
            DB::commit();

            // Devolver la respuesta con el miembro actualizado
            return response()->json([
                'status' => 'success',
                'data' => new MemberResource($member)
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre algún error, revertir la transacción
            DB::rollBack();

            // Devolver un error
            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un error al actualizar el miembro. Por favor, intente nuevamente.'
            ], 500);
        }
    }

    /**
     * Elimina un socio específico por su ID.
     * 
     * Este método recibe un ID de socio, busca el socio en la base de datos y elimina el socio.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     */
    public function destroy($id): JsonResponse
    {
        $member = User::findOrFail($id);
        $member->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Socio eliminado satisfactoriamente'
        ], 200);
    }

    /**
     * Muestra el perfil del socio autenticado.
     * 
     * Este método obtiene el socio autenticado, busca el socio en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del socio.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio.
     */
    public function showProfile(): JsonResponse
    {
        $member = Auth::user()->member;

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 200);
    }

    /**
     * Actualiza el perfil del socio autenticado.
     * 
     * Este método recibe una solicitud de actualización de socio, valida los datos y actualiza el socio en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del socio actualizado en formato JSON.
     *
     * @param UpdateMemberRequest $request La solicitud de actualización de socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio actualizado.
     */
    public function updateProfile(UpdateMemberRequest $request): JsonResponse
    {
        $member = Auth::user()->member;
        $member->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 200);
    }
}
