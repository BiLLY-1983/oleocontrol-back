<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\UserResource;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Mail\NewUserWelcomeEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    /**
     * Muestra todos los empleados.
     * 
     * Este método obtiene todos los empleados de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los empleados.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los empleados.
     * 
     * @OA\Get(
     *     path="/api/employees",
     *     summary="Listar todos los empleados",
     *     tags={"Empleados"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empleados",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/EmployeeResource"))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => EmployeeResource::collection(Employee::all())
        ], 200);
    }

    /**
     * Crea un nuevo empleado junto con el usuario y rol correspondiente.
     * 
     * Este método crea un nuevo empleado, su usuario asociado y le asigna el rol de "Empleado". La creación se realiza dentro de una transacción para garantizar la integridad de los datos.
     * La respuesta incluye un estado de éxito y los datos del usuario creado.
     *
     * @param StoreEmployeeRequest $request La solicitud de creación de empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del usuario creado.
     * 
     * @OA\Post(
     *     path="/api/employees",
     *     summary="Crear un nuevo empleado",
     *     tags={"Empleados"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "dni", "email", "phone", "status", "department_id"},
     *             @OA\Property(property="first_name", type="string", example="Laura"),
     *             @OA\Property(property="last_name", type="string", example="Martínez"),
     *             @OA\Property(property="dni", type="string", example="12345678Z"),
     *             @OA\Property(property="email", type="string", format="email", example="laura@example.com"),
     *             @OA\Property(property="phone", type="string", example="666555444"),
     *             @OA\Property(property="status", type="string", example="activo"),
     *             @OA\Property(property="department_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empleado creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource")
     *         )
     *     )
     * )
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        DB::beginTransaction(); // Iniciar la transacción

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

            // Asignar rol al usuario
            $roleName = 'Empleado';

            // Buscar el rol en la base de datos
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                throw new \Exception("El rol '{$roleName}' no existe.");
            }

            // Asociar el rol al usuario
            $user->roles()->attach($role->id);

            // Crear el socio
            $employee = Employee::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
            ]);

            // Verificar si el socio se creó correctamente
            if (!$employee) {
                throw new \Exception('No se pudo crear el empleado.');
            }

            DB::commit(); // Confirmar la transacción

            // Enviar el email con la nueva contraseña
            Mail::to($user->email)->send(new NewUserWelcomeEmail($full_name, $username, $generatedPassword));

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
     * Muestra un empleado específico por su ID.
     * 
     * Este método recibe un ID de empleado, busca el empleado en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del empleado.
     *
     * @param int $id El ID del empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado.
     * @OA\Get(
     *     path="/api/employees/{id}",
     *     summary="Mostrar un empleado",
     *     tags={"Empleados"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos del empleado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/EmployeeResource")
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 200);
    }

    /**
     * Actualiza un empleado específico por su ID.
     * 
     * Este método recibe una solicitud de actualización de empleado, valida los datos y actualiza el empleado y su usuario asociado en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del empleado actualizado en formato JSON.
     *
     * @param UpdateEmployeeRequest $request La solicitud de actualización de empleado.
     * @param int $id El ID del empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado actualizado.
     * @OA\Put(
     *     path="/api/employees/{id}",
     *     summary="Actualizar un empleado",
     *     tags={"Empleados"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="username", type="string", example="laura.martinezZ"),
     *                 @OA\Property(property="first_name", type="string", example="Laura"),
     *                 @OA\Property(property="last_name", type="string", example="Martínez"),
     *                 @OA\Property(property="dni", type="string", example="12345678Z"),
     *                 @OA\Property(property="email", type="string", example="laura@example.com"),
     *                 @OA\Property(property="phone", type="string", example="666555444"),
     *                 @OA\Property(property="status", type="string", example="activo")
     *             ),
     *             @OA\Property(property="department", type="object",
     *                 @OA\Property(property="id", type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/EmployeeResource")
     *         )
     *     )
     * )
     */
    public function update(UpdateEmployeeRequest $request, $id): JsonResponse
    {
        DB::beginTransaction(); // Inicia la transacción

        try {
            $employee = Employee::findOrFail($id);

            $userData = $request->input('user');

            $departmentId = $request->input('department_id');

            if ($departmentId) {
                $employee->department_id = $departmentId;
                $employee->save();
            }

            $user = $employee->user; // Obtener el usuario asociado al miembro

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
                'data' => new EmployeeResource($employee)
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
     * Elimina un empleado específico por su ID.
     * 
     * Este método recibe un ID de empleado, busca el empleado en la base de datos y elimina el empleado.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID del empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     * @OA\Delete(
     *     path="/api/employees/{id}",
     *     summary="Eliminar un empleado",
     *     tags={"Empleados"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario del empleado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Empleado eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $employee = User::findOrFail($id);
        $employee->delete();

        return response()->json([
            'status' => 'success',
        ], 204);
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
