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
use App\Mail\NewUserWelcomeEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class MemberController extends Controller
{
    /**
     * Muestra todos los socios.
     * 
     * Este método obtiene todos los socios de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los socios.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los socios.
     * 
     * @OA\Get(
     *     path="/api/members",
     *     tags={"Socios"},
     *     summary="Listar todos los socios",
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de socios",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MemberResource"))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => MemberResource::collection(Member::all())
        ], 200);
    }

    /**
     * Crea un nuevo miembro junto con su usuario y rol correspondiente.
     * 
     * Este método crea un nuevo miembro, su usuario asociado y le asigna el rol de "Socio". La creación se realiza dentro de una transacción para garantizar la integridad de los datos.
     * La respuesta incluye un estado de éxito y los datos del usuario creado.
     *
     * @param StoreMemberRequest $request La solicitud de creación de miembro.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del usuario creado.
     * 
     * @OA\Post(
     *     path="/api/members",
     *     summary="Crear un nuevo miembro",
     *     tags={"Socios"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "dni", "email", "phone", "status"},
     *             @OA\Property(property="first_name", type="string", example="Juan"),
     *             @OA\Property(property="last_name", type="string", example="Pérez"),
     *             @OA\Property(property="dni", type="string", example="12345678Z"),
     *             @OA\Property(property="email", type="string", format="email", example="juan.perez@example.com"),
     *             @OA\Property(property="phone", type="string", example="612345678"),
     *             @OA\Property(property="status", type="string", example="activo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Miembro creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     )
     * )
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {


            // Generar contraseña aleatoria
            $generatedPassword = $this->generateSecurePassword();

            // Generar username automáticamente
            $first = explode(' ', trim($request->first_name))[0];
            $last = explode(' ', trim($request->last_name))[0];
            $dniLetter = strtoupper(substr($request->dni, -1));
            $username = strtolower("{$first}.{$last}{$dniLetter}");

            $username = strtolower("{$first}.{$last}{$dniLetter}");
            $count = User::where('username', $username)->count();
            if ($count > 0) {
                $username .= rand(10, 99);
            }

            // Formar nombre completo
            $full_name = trim("{$request->first_name} {$request->last_name}");

            $user = User::create([
                'username' => $username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dni' => $request->dni,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($generatedPassword),
                'status' => $request->status
            ]);

            $roleName = 'Socio';

            // Buscar el rol en la base de datos
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                throw new \Exception("El rol '{$roleName}' no existe.");
            }

            $user->roles()->attach($role->id);

            $member = Member::create([
                'user_id' => $user->id,
                'member_number' => 1000 + $user->id,
            ]);

            if (!$member) {
                throw new \Exception('No se pudo crear el socio.');
            }

            DB::commit();

            // Enviar el email con la nueva contraseña
            Mail::to($user->email)->send(new NewUserWelcomeEmail($full_name, $username, $generatedPassword));

            return response()->json([
                'status' => 'success',
                'data' => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

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
     * @OA\Get(
     *     path="/api/members/{id}",
     *     tags={"Socios"},
     *     summary="Obtener los datos de un socio por ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del socio",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos del socio",
     *         @OA\JsonContent(ref="#/components/schemas/MemberResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Socio no encontrado"
     *     )
     * )
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
     * Este método recibe una solicitud de actualización de socio, valida los datos y actualiza tanto el miembro como el usuario asociado en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del socio actualizado.
     * Si ocurre algún error, se realiza un rollback de la transacción y se devuelve un mensaje de error.
     *
     * @param UpdateMemberRequest $request La solicitud de actualización de socio.
     * @param int $id El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio actualizado. En caso de error, se devuelve el mensaje de error.
     * @OA\Put(
     *     path="/api/members/{id}",
     *     tags={"Socios"},
     *     summary="Actualizar datos de un socio",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del socio",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "dni", "email", "phone", "status"},
     *             @OA\Property(property="first_name", type="string", example="Juan"),
     *             @OA\Property(property="last_name", type="string", example="Pérez"),
     *             @OA\Property(property="dni", type="string", example="12345678Z"),
     *             @OA\Property(property="email", type="string", format="email", example="juan.perez@example.com"),
     *             @OA\Property(property="phone", type="string", example="612345678"),
     *             @OA\Property(property="status", type="string", example="activo")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Socio actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/MemberResource")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al actualizar el socio"
     *     )
     * )
     */
    public function update(UpdateMemberRequest $request, $id): JsonResponse
    {
        DB::beginTransaction(); // Inicia la transacción

        try {
            // Buscar el miembro por su ID
            $member = Member::findOrFail($id);

            // Preparar los datos a actualizar
            $updateData = $request->only(['status']); // Deja 'member_number' fuera si está vacío

            // Si 'member_number' no está vacío, lo agregamos a la actualización
            if ($request->has('member_number') && !is_null($request->input('member_number'))) {
                $updateData['member_number'] = $request->input('member_number');
                $member->update($updateData);
            }

            // Obtener los datos del usuario del request
            $userData = $request->input('user');
            $user = $member->user; // Obtener el usuario asociado al miembro

            // Actualizar los campos del usuario
            if ($user) {
                $user->update([
                    'username' => $userData['username'] ?? $user->username,
                    'first_name' => $userData['first_name'] ?? $user->first_name,
                    'last_name' => $userData['last_name'] ?? $user->last_name,
                    'dni' => $userData['dni'] ?? $user->dni,
                    'email' => $userData['email'] ?? $user->email,
                    'phone' => $userData['phone'] ?? $user->phone,
                    'status' => $userData['status'] ?? $user->status,
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
                'message' => 'Hubo un error al actualizar el miembro. Por favor, intente nuevamente. '
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
     * @OA\Delete(
     *     path="/api/members/{id}",
     *     tags={"Socios"},
     *     summary="Eliminar un socio",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del socio",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Socio eliminado satisfactoriamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Socio no encontrado"
     *     )
     * )
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
     * Genera una contraseña segura aleatoria.
     * 
     * Este método genera una contraseña aleatoria cumpliendo con los requisitos de incluir al menos una letra minúscula, una mayúscula y un número. La longitud por defecto es de 10 caracteres.
     *
     * @param int $length La longitud deseada para la contraseña (por defecto es 10).
     * @return string La contraseña generada aleatoriamente.
     */
    private function generateSecurePassword($length = 10)
    {
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $all = $lower . $upper . $numbers;

        $password = $lower[random_int(0, 25)];
        $password .= $upper[random_int(0, 25)];
        $password .= $numbers[random_int(0, 9)];

        for ($i = 3; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }
}
