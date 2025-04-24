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
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => EmployeeResource::collection(Employee::all())
        ], 200);
    }

    /**
     * Muestra el empleado asociado a un usuario específico.
     * 
     * Este método recibe un ID de usuario, busca el empleado correspondiente y devuelve una respuesta JSON con un estado de éxito y los datos del empleado.
     *
     * @param int $userId El ID del usuario.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado.
     */
    public function indexByUser($userId)
    {
        // Buscar el empleado cuyo user_id coincida con el id proporcionado
        $employee = Employee::where('user_id', $userId)->first();

        // Verificar si el empleado existe
        if ($employee) {
            return response()->json([
                'status' => 'success',
                'data' => new EmployeeResource($employee)
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Empleado no encontrado'
            ], 404);
        }
    }

    /**
     * Crea un nuevo empleado.
     * 
     * Este método recibe una solicitud de creación de empleado, valida los datos y crea un nuevo empleado en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del empleado creado en formato JSON.
     *
     * @param StoreEmployeeRequest $request La solicitud de creación de empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado creado.
     */
    public function storeOld(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 201);
    }

    /**
     * Crea un nuevo empleado junto con el usuario y rol correspondiente.
     * 
     * Este método crea un nuevo empleado, su usuario asociado y le asigna el rol de "Empleado". La creación se realiza dentro de una transacción para garantizar la integridad de los datos.
     * La respuesta incluye un estado de éxito y los datos del usuario creado.
     *
     * @param StoreEmployeeRequest $request La solicitud de creación de empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del usuario creado.
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
            ]);
            

/* 
            // Crear el usuario
            $user = User::create($request->only([
                'username',
                'first_name',
                'last_name',
                'dni',
                'email',
                'password',
                'phone',
            ])); */

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
     */
    public function update(UpdateEmployeeRequest $request, $id): JsonResponse
    {
        DB::beginTransaction(); // Inicia la transacción

        try {
            $employee = Employee::findOrFail($id);

            $userData = $request->input('user');
            $departmentData = $request->input('department');

            // Actualiza el departamento del empleado
            if ($departmentData && isset($departmentData['id'])) {
                $employee->department_id = $departmentData['id']; // Actualiza el departamento
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
     * Muestra el perfil del empleado autenticado.
     * 
     * Este método obtiene el empleado autenticado y devuelve una respuesta JSON con un estado de éxito y los datos del empleado.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado.
     */
    public function showProfile(): JsonResponse
    {
        $employee = Auth::user()->employee;

        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 200);
    }

    /**
     * Actualiza el perfil del empleado autenticado.
     * 
     * Este método recibe una solicitud de actualización de empleado, valida los datos y actualiza el empleado en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del empleado actualizado en formato JSON.
     *
     * @param UpdateEmployeeRequest $request La solicitud de actualización de empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del empleado actualizado.
     */
    public function updateProfile(UpdateEmployeeRequest $request): JsonResponse
    {
        $employee = Auth::user()->employee;
        $employee->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
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
