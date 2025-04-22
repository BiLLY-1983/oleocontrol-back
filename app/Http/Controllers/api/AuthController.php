<?php

namespace App\Http\Controllers\api;

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
     * Cierre de sesión de un usuario.
     *
     * @param Request $request La solicitud de cierre de sesión.
     * @return JsonResponse Respuesta JSON con un mensaje de éxito o denegado.
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
