<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\PasswordResetEmail;
use Illuminate\Support\Facades\Mail;

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
    /* public function storeOld(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ], 201);
    } */

    public function store(StoreUserRequest $request): JsonResponse
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
            $roleName = $request->user_type; // Puede ser 'Administrador', 'Invitado', 'Socio', o 'Empleado'

            // Buscar el rol en la base de datos
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                throw new \Exception("El rol '{$roleName}' no existe.");
            }

            // Asociar el rol al usuario
            $user->roles()->attach($role->id);

            // Manejar roles específicos
            if ($roleName === 'Socio') {
                // Crear el socio
                $member = Member::create([
                    'user_id' => $user->id,
                    'member_number' => 1000 + $user->id,
                ]);

                // Verificar si el socio se creó correctamente
                if (!$member) {
                    throw new \Exception('No se pudo crear el socio.');
                }
            } elseif ($roleName === 'Empleado') {
                // Validar que el departamento esté presente
                if (!$request->has('department_id')) {
                    throw new \Exception('El campo department_id es obligatorio para empleados.');
                }

                // Crear el empleado
                $employee = Employee::create([
                    'user_id' => $user->id,
                    'department_id' => $request->department_id,
                ]);

                // Verificar si el empleado se creó correctamente
                if (!$employee) {
                    throw new \Exception('No se pudo crear el empleado.');
                }
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
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = Auth::user();

        $data = $request->validated();

        // Si viene la contraseña, encriptarla
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

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


    public function resetPasswordRequest(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)
            ->where('username', $request->username)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales no válidas'
            ], 404);
        }

        // Generar una contraseña aleatoria que cumpla con las reglas
        $newPassword = $this->generateSecurePassword();

        // Guardar la nueva contraseña
        $user->password = Hash::make($newPassword);
        $user->save();

        // Enviar el email con la nueva contraseña
        Mail::to($user->email)->send(new PasswordResetEmail($user->username, $newPassword));

        return response()->json([
            'status' => 'success',
            'message' => 'Contraseña restablecida con éxito, se ha enviado la nueva contraseña por correo.',
            'data' => $newPassword,
        ]);
    }


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
