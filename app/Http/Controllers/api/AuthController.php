<?php

namespace App\Http\Controllers\api;

/**
 * @OA\Info(
 *     title="API OleoControl",
 *     version="1.0.0",
 *     description="Documentación de la API de la aplicación OleoControl"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http", 
 *     scheme="bearer",  
 *     bearerFormat="JWT", 
 *     description="Token de autenticación Bearer (usado por Sanctum)"
 * )
 */

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de un nuevo usuario.
     *
     * @param RegisterRequest $request La solicitud de registro con los datos del usuario.
     * @return JsonResponse Respuesta JSON con el token de autenticación y el usuario registrado.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated() + [
            'password' => Hash::make($request->password),
            'status' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201);
    }

    /**
     * Inicio de sesión de un usuario.
     * 
     * Este método recibe una solicitud de inicio de sesión con el nombre de usuario y la contraseña. Si las credenciales son válidas y el usuario está activo, se genera un token de autenticación para el usuario. Si las credenciales no son correctas o la cuenta está desactivada, se devuelve un mensaje de error adecuado.
     *
     * @param LoginRequest $request La solicitud de inicio de sesión con el nombre de usuario y la contraseña.
     * @return JsonResponse Respuesta JSON con el token de autenticación y los datos del usuario si el inicio de sesión es exitoso. En caso de error, devuelve un mensaje con el código de estado correspondiente.
     * @throws ValidationException Si las credenciales proporcionadas son incorrectas o el usuario está desactivado.
     *
     * 
     * @OA\Post(
     *     path="/api/login",
     *     summary="Iniciar sesión",
     *     description="Autentica a un usuario usando su nombre de usuario y contraseña. Devuelve un token de acceso si las credenciales son válidas y el usuario está activo.",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", example="AdminPruebas"),
     *             @OA\Property(property="password", type="string", example="Password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="1|X9N..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="user", type="object", description="Información del usuario autenticado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales incorrectas",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Credenciales incorrectas.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Cuenta desactivada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Esta cuenta está desactivada.")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales incorrectas.'
            ], 401);
        }

        if (!$user->status) {
            return response()->json([
                'status' => 'error',
                'message' => 'Esta cuenta está desactivada.'
            ], 403);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->load('roles');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Cierre de sesión del usuario autenticado.
     * 
     * Este método invalida el token de acceso actual del usuario autenticado mediante Sanctum, cerrando su sesión. 
     * Si no hay un usuario autenticado, devuelve un mensaje de error con estado 403.
     *
     * @param Request $request La solicitud HTTP actual que contiene el token del usuario autenticado.
     * @return JsonResponse Mensaje indicando si el cierre de sesión fue exitoso o si hubo un error de autorización.
     *
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Cerrar sesión",
     *     description="Cierra la sesión del usuario autenticado eliminando el token.",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout exitoso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado, token inválido o expirado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Token inválido o expirado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logout exitoso']);
        }

        return response()->json(['message' => 'Acceso denegado'], 403);
    }
}
