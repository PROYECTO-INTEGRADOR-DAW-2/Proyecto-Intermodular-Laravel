<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'nombre_usuario' => ['required'],
                'contraseña' => ['required'],
            ]);

            logger()->info('Attempting login for: ' . $request->nombre_usuario);

            if (!Auth::attempt(['nombre_usuario' => $request->nombre_usuario, 'password' => $request->contraseña])) {
                logger()->warning('Login failed for: ' . $request->nombre_usuario);
                return $this->sendError('Unauthorised', ['error' => 'Credencials incorrectes'], 401);
            }

            $user = Auth::user();
            logger()->info('Login successful for ID: ' . $user->id);

            $result = [
                'token' => $user->createToken('api')->plainTextToken,
                'user' => [
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'apellidos' => $user->apellidos,
                    'nombre_usuario' => $user->nombre_usuario,
                    'email' => $user->email,
                ],
            ];

            return $this->sendResponse($result, 'User signed in', 200);
        }
        catch (\Exception $e) {
            logger()->error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'nombre_usuario' => ['required', 'string', 'max:255', 'unique:users,nombre_usuario'],
            'email' => ['required', 'email', 'unique:users,email'],
            'contraseña' => ['required', 'min:6'],
            'confirm_contraseña' => ['required', 'same:contraseña'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 422);
        }

        $data = $validator->validated();

        $user = User::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'nombre_usuario' => $data['nombre_usuario'],
            'email' => $data['email'],
            'contraseña' => Hash::make($data['contraseña']),
        ]);

        $result = [
            'token' => $user->createToken('api')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'nombre_usuario' => $user->nombre_usuario,
                'email' => $user->email,
            ],
        ];

        return $this->sendResponse($result, 'User created successfully', 201);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->currentAccessToken()->delete();
            return $this->sendResponse(['nombre' => $user->nombre], 'User successfully signed out', 200);
        }

        return $this->sendError('No active session', [], 401);
    }
}