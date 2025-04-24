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
use App\Mail\PasswordResetEmail;
use App\Mail\NewUserWelcomeEmail;
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
     * Crea y almacena un nuevo usuario en la base de datos y asigna un rol y perfil específico (Socio o Empleado).
     *
     * Este método maneja la creación de un nuevo usuario, asignación de roles y creación de un perfil dependiendo del tipo de rol. 
     * Si el rol es 'Socio', se crea un miembro con un número de socio único. Si el rol es 'Empleado', se asigna el departamento correspondiente.
     * El proceso se maneja dentro de una transacción para asegurar la integridad de los datos.
     * 
     * @param  \App\Http\Requests\StoreUserRequest  $request  La solicitud que contiene los datos del usuario a crear.
     * 
     * @return \Illuminate\Http\JsonResponse  La respuesta JSON con el estado de la operación y los datos del usuario creado.
     * 
     * @throws \Exception  Lanza una excepción si ocurre algún error al crear el usuario, rol, miembro o empleado.
     */
    public function store(StoreUserRequest $request): JsonResponse
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

/*             // Validar que la letra del DNI sea correcta
            $dniNumber = substr($request->dni, 0, -1);
            $dniLetter = strtoupper(substr($request->dni, -1));
            $letters = "TRWAGMYFPDXBNJZSQVHLCKE";
            $expectedLetter = $letters[$dniNumber % 23];

            if ($dniLetter !== $expectedLetter) {
                throw new \Exception("La letra del DNI no es válida.");
            } */

            $user = User::create([
                'username' => $username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dni' => $request->dni,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($generatedPassword),
            ]);

            

           /*  $user = User::create($request->only([
                'username',
                'first_name',
                'last_name',
                'dni',
                'email',
                'password',
                'phone',
            ])); */

            $roleName = $request->user_type;

            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                throw new \Exception("El rol '{$roleName}' no existe.");
            }

            $user->roles()->attach($role->id);

            if ($roleName === 'Socio') {
                $member = Member::create([
                    'user_id' => $user->id,
                    'member_number' => 1000 + $user->id,
                ]);

                if (!$member) {
                    throw new \Exception('No se pudo crear el socio.');
                }
            } elseif ($roleName === 'Empleado') {
                if (!$request->has('department_id')) {
                    throw new \Exception('El campo department_id es obligatorio para empleados.');
                }

                $employee = Employee::create([
                    'user_id' => $user->id,
                    'department_id' => $request->department_id,
                ]);

                if (!$employee) {
                    throw new \Exception('No se pudo crear el empleado.');
                }
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

    /**
     * Solicita el restablecimiento de la contraseña para un usuario.
     * 
     * Este método recibe una solicitud con el correo electrónico y nombre de usuario, verifica que las credenciales sean correctas, genera una nueva contraseña aleatoria y la guarda en la base de datos. Además, envía un correo electrónico al usuario con la nueva contraseña.
     *
     * @param ResetPasswordRequest $request La solicitud de restablecimiento de contraseña.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el estado de éxito, mensaje y nueva contraseña.
     */
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
