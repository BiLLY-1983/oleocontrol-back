<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
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
     * @param LoginRequest $request La solicitud de inicio de sesión con el nombre de usuario y la contraseña.
     * @return JsonResponse Respuesta JSON con el token de autenticación y los datos del usuario.
     * @throws ValidationException Si las credenciales proporcionadas son incorrectas.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if (!$user->status) {
            throw ValidationException::withMessages([
                'username' => ['Esta cuenta está desactivada.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
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