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
     * Obtener una lista de usuarios.
     * 
     * Este método devuelve todos los usuarios registrados en la base de datos, utilizando la clase `UserResource` para formatear la respuesta. 
     * Solo usuarios autenticados pueden acceder a esta ruta.
     *
     * @param Request $request Datos necesarios para realizar la acción (en este caso no se requieren parámetros).
     * @return JsonResponse Devuelve una respuesta JSON con el estado 'success' y una lista de usuarios.
     * @throws \Exception Excepción si no se puede obtener la lista de usuarios.
     *
     * @OA\Get(
     *     path="/api/users",
     *     summary="Obtener todos los usuarios",
     *     description="Devuelve una lista de todos los usuarios registrados en la base de datos.",
     *     tags={"Usuarios"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios obtenida exitosamente.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="email", type="string", example="juan.perez@ejemplo.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Token de autenticación no válido o no proporcionado.")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => UserResource::collection(User::all())
        ], 200);
    }

    /**
     * Crear un nuevo usuario.
     * 
     * Este método crea un nuevo usuario en la base de datos, generando automáticamente un nombre de usuario, asignando un rol y enviando un correo de bienvenida con la contraseña generada. El proceso está envuelto en una transacción para garantizar la integridad de los datos.
     * Solo los administradores pueden acceder a esta ruta.
     *
     * @param StoreUserRequest $request Datos necesarios para crear un nuevo usuario (nombre, apellidos, DNI, email, teléfono, rol, etc.).
     * @return JsonResponse Respuesta con el estado 'success' y los datos del usuario recién creado.
     * @throws \Exception Si ocurre un error durante la creación del usuario o en el proceso de asignación de roles y creación de miembro o empleado.
     *
     * @OA\Post(
     *     path="/api/users",
     *     summary="Crear un nuevo usuario",
     *     description="Crea un nuevo usuario, generando un nombre de usuario y contraseña aleatoria, asignando un rol y enviando un correo de bienvenida con la nueva contraseña.",
     *     tags={"Usuarios"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "dni", "email", "phone", "status", "user_type"},
     *             @OA\Property(property="first_name", type="string", example="Juan"),
     *             @OA\Property(property="last_name", type="string", example="Pérez"),
     *             @OA\Property(property="dni", type="string", example="12345678Z"),
     *             @OA\Property(property="email", type="string", example="juan.perez@ejemplo.com"),
     *             @OA\Property(property="phone", type="string", example="123456789"),
     *             @OA\Property(property="status", type="string", example="active"),
     *             @OA\Property(property="user_type", type="string", example="Socio"),
     *             @OA\Property(property="department_id", type="integer", example=1) 
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario creado exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta, falta algún dato requerido.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="El campo department_id es obligatorio para empleados.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación de administrador.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No se pudo crear el socio.")
     *         )
     *     )
     * )
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
            $username = strtolower("{$first}.{$last}.{$dniLetter}");

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
     * Obtener un usuario por ID.
     * 
     * Este endpoint permite obtener un usuario específico de la base de datos utilizando su ID. La ruta está protegida por autenticación utilizando Sanctum y el rol de administrador debe estar presente para poder acceder. Si el usuario no es encontrado o el rol no es el adecuado, se devolverán errores correspondientes.
     *
     * @param Request $request Los datos necesarios para realizar la consulta, en este caso, el ID del usuario.
     * @return JsonResponse Respuesta con los datos del usuario o un mensaje de error en caso de fallo.
     * @throws ModelNotFoundException Si el usuario con el ID especificado no es encontrado.
     * @throws AuthorizationException Si el usuario no tiene los permisos necesarios (no es administrador).
     *
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Obtiene un usuario por ID",
     *     description="Este endpoint permite obtener un usuario específico de la base de datos usando su ID. Solo accesible para administradores autenticados.",
     *     tags={"Usuarios"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Usuario no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado. Se requiere ser administrador.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado")
     *         )
     *     )
     * )
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
     * Obtener el perfil del usuario autenticado.
     * 
     * Este endpoint devuelve los datos del perfil del usuario actualmente autenticado mediante Sanctum. Si el token no es válido o ha expirado, se devuelve un error de autenticación.
     *
     * @return JsonResponse Contiene los datos del usuario autenticado o un mensaje de error si no está autenticado.
     *
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Obtener el perfil del usuario autenticado",
     *     description="Devuelve los datos del usuario que ha iniciado sesión. Requiere autenticación mediante token Bearer.",
     *     tags={"Usuarios"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Datos del usuario autenticado obtenidos correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                 @OA\Property(property="email", type="string", example="juan@example.com"),
     *                 @OA\Property(property="created_at", type="string", example="2024-09-01T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-09-20T10:11:12Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Usuario no autenticado")
     *         )
     *     )
     * )
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
     * Actualizar un usuario por ID.
     * 
     * Este endpoint permite actualizar los detalles de un usuario específico mediante su ID. Solo los administradores autenticados pueden realizar esta acción. Si el usuario no es encontrado, se lanzará una excepción, y si el administrador no tiene permisos, se denegará el acceso.
     *
     * @param UpdateUserRequest $request Datos para actualizar el usuario, validados por el formulario de validación `UpdateUserRequest`.
     * @param int $id ID del usuario a actualizar.
     * @return JsonResponse Respuesta con el estado de la actualización y los datos del usuario actualizado.
     * @throws ModelNotFoundException Si el usuario con el ID especificado no es encontrado.
     * @throws AuthorizationException Si el usuario no tiene los permisos necesarios (no es administrador).
     *
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Actualizar un usuario por ID",
     *     description="Este endpoint permite actualizar los detalles de un usuario utilizando su ID. Solo accesible para administradores autenticados.",
     *     tags={"Usuarios"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario que se desea actualizar",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "phone", "status"},
     *             @OA\Property(property="first_name", type="string", example="Juan"),
     *             @OA\Property(property="last_name", type="string", example="Pérez"),
     *             @OA\Property(property="email", type="string", example="juan.perez@example.com"),
     *             @OA\Property(property="phone", type="string", example="123456789"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario actualizado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/UserResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Usuario no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado. Se requiere ser administrador.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado")
     *         )
     *     )
     * )
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
     * Actualizar el perfil del usuario autenticado.
     * 
     * Este endpoint permite al usuario autenticado actualizar su nombre, correo electrónico y/o contraseña. Si se proporciona una nueva contraseña, esta será encriptada antes de ser almacenada.
     * 
     * @param UpdateProfileRequest $request Datos validados para actualizar el perfil del usuario autenticado.
     * @return JsonResponse Devuelve los datos actualizados del usuario o un mensaje de error.
     *
     * @OA\Put(
     *     path="/api/profile",
     *     summary="Actualizar perfil del usuario autenticado",
     *     description="Actualiza los datos del usuario que ha iniciado sesión. Requiere autenticación con token Bearer.",
     *     tags={"Usuarios"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Juan Actualizado"),
     *             @OA\Property(property="email", type="string", format="email", example="nuevo.email@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="nuevaContraseña123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Perfil actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Juan Actualizado"),
     *                 @OA\Property(property="email", type="string", example="nuevo.email@example.com"),
     *                 @OA\Property(property="created_at", type="string", example="2024-09-01T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-10-01T10:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación en los datos proporcionados",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="El email ya ha sido registrado"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Usuario no autenticado")
     *         )
     *     )
     * )
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
     * Eliminar un usuario por ID.
     * 
     * Este endpoint permite eliminar un usuario específico mediante su ID. Solo los administradores autenticados pueden realizar esta acción. Si el usuario no es encontrado, se lanzará una excepción, y si el administrador no tiene permisos, se denegará el acceso.
     *
     * @param int $id ID del usuario a eliminar.
     * @return JsonResponse Respuesta con el estado de la eliminación y un mensaje de éxito.
     * @throws ModelNotFoundException Si el usuario con el ID especificado no es encontrado.
     * @throws AuthorizationException Si el usuario no tiene los permisos necesarios (no es administrador).
     *
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Eliminar un usuario por ID",
     *     description="Este endpoint permite eliminar un usuario utilizando su ID. Solo accesible para administradores autenticados.",
     *     tags={"Usuarios"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario que se desea eliminar",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Usuario eliminado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Usuario eliminado satisfactoriamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Usuario no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado. Se requiere ser administrador.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Usuario eliminado satisfactoriamente'
        ], 204);
    }

    /**
     * Solicita el restablecimiento de la contraseña para un usuario.
     * 
     * Este método recibe una solicitud con el correo electrónico y nombre de usuario, verifica que las credenciales sean correctas, genera una nueva contraseña aleatoria y la guarda en la base de datos. Además, envía un correo electrónico al usuario con la nueva contraseña.
     *
     * @param ResetPasswordRequest $request La solicitud de restablecimiento de contraseña.
     * @return JsonResponse Respuesta JSON con el estado de éxito, mensaje y nueva contraseña.
     * @OA\Post(
     *     path="/reset-password-request",
     *     summary="Restablecer la contraseña de un usuario",
     *     description="Este endpoint permite restablecer la contraseña de un usuario a partir de su correo electrónico y nombre de usuario. Si las credenciales son válidas, se genera una nueva contraseña aleatoria, se guarda y se envía por correo electrónico al usuario.",
     *     operationId="resetPasswordRequest",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","username"},
     *             @OA\Property(property="email", type="string", format="email", example="usuario@example.com"),
     *             @OA\Property(property="username", type="string", example="nombredeusuario")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contraseña restablecida correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Contraseña restablecida con éxito, se ha enviado la nueva contraseña por correo."),
     *             @OA\Property(property="data", type="string", example="NuevaContraseña123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Credenciales no válidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Credenciales no válidas")
     *         )
     *     )
     * )
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

        $newPassword = $this->generateSecurePassword();

        $user->password = Hash::make($newPassword);
        $user->save();

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
