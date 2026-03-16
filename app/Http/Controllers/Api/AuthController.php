<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "J&A Sports API",
    version: "1.0.0",
    description: "API REST del projecte intermodular J&A Sports. Autenticació via Bearer Token (Laravel Sanctum).",
    contact: new OA\Contact(email: "admin@jasports.local")
)]
#[OA\Server(url: "http://localhost:8000", description: "Servidor local Docker")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "Sanctum",
    description: "Introdueix el token obtingut en /api/login. Format: Bearer {token}"
)]
#[OA\Tag(name: "Auth", description: "Autenticació d'usuaris")]
#[OA\Tag(name: "Products", description: "Gestió de productes")]
#[OA\Tag(name: "Orders", description: "Gestió de comandes")]
#[OA\Tag(name: "Cart", description: "Carret de la compra")]
#[OA\Tag(name: "Reviews", description: "Ressenyes de productes")]
#[OA\Tag(name: "OAuth", description: "Login social amb Google (OAuth2)")]
class AuthController extends BaseController
{
    #[OA\Post(
        path: "/api/login",
        summary: "Iniciar sessió",
        description: "Retorna un Bearer token de Sanctum per autenticar les peticions.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "admin@jasports.local"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login correcte",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "token", type: "string", example: "1|abc123..."),
                            new OA\Property(property: "name", type: "string", example: "Admin"),
                            new OA\Property(property: "email", type: "string", example: "admin@jasports.local"),
                            new OA\Property(property: "role", type: "string", example: "admin"),
                            new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string"))
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Credencials incorrectes"),
            new OA\Response(response: 422, description: "Validació fallida")
        ],
        tags: ["Auth"]
    )]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->sendError('Unauthorised', ['error' => 'Credencials incorrectes'], 401);
        }

        $user = $request->user();
        $result = [
            'token' => $user->createToken('api')->plainTextToken,
            'id'    => $user->id,
            'name'  => $user->nombre,
            'email' => $user->email,
            'role'  => $user->role,                          // direct column
            'roles' => $user->roles->pluck('name'),          // pivot table roles
        ];

        return $this->sendResponse($result, 'User signed in', 200);
    }

    #[OA\Post(
        path: "/api/register",
        summary: "Registrar nou usuari",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Joan Garcia"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "joan@example.com"),
                    new OA\Property(property: "password", type: "string", example: "secret123"),
                    new OA\Property(property: "password_confirmation", type: "string", example: "secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Usuari creat correctament"),
            new OA\Response(response: 422, description: "Validació fallida")
        ],
        tags: ["Auth"]
    )]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','min:6','confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 422);
        }

        $data = $validator->validated();

        // Split name into nombre and apellidos
        $parts = explode(' ', $data['name'], 2);
        $nombre = $parts[0];
        $apellidos = $parts[1] ?? '';

        $user = User::create([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'nombre_usuario' => explode('@', $data['email'])[0],
            'email' => $data['email'],
            'contraseña' => $data['password'],
        ]);

        $result = [
            'token' => $user->createToken('api')->plainTextToken,
            'id'    => $user->id,
            'name'  => $user->nombre,
            'email' => $user->email,
        ];

        return $this->sendResponse($result, 'User created successfully', 201);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Tancar sessió",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Sessió tancada correctament"),
            new OA\Response(response: 401, description: "No autenticat")
        ],
        tags: ["Auth"]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(['name' => $request->user()->name], 'User successfully signed out', 200);
    }

    #[OA\Put(
        path: "/api/user/profile",
        summary: "Actualitzar perfil d'usuari",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Joan Garcia"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "joan@example.com"),
                    new OA\Property(property: "password", type: "string", example: "newsecret123"),
                    new OA\Property(property: "password_confirmation", type: "string", example: "newsecret123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Perfil actualitzat correctament"),
            new OA\Response(response: 422, description: "Validació fallida")
        ],
        tags: ["Auth"]
    )]
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error validation', $validator->errors(), 422);
        }

        $data = $validator->validated();

        // Split name into nombre and apellidos
        $parts = explode(' ', $data['name'], 2);
        $nombre = $parts[0];
        $apellidos = $parts[1] ?? '';

        $user->nombre = $nombre;
        $user->apellidos = $apellidos;
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->contraseña = $data['password']; // Mutator handles hashing if set in model, otherwise hash here
            // Checking model: casts 'contraseña' => 'hashed', so direct assignment works if using standard Laravel 10/11 casting
            // However, older implementations might need Hash::make. 
            // Model has 'contraseña' => 'hashed' in casts(), so it should auto-hash.
        }

        $user->save();

        $result = [
            'name' => $user->nombre . ($user->apellidos ? ' ' . $user->apellidos : ''),
            'email' => $user->email,
        ];

        return $this->sendResponse($result, 'Profile updated successfully', 200);
    }
}