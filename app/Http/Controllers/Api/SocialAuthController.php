<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Attributes as OA;

class SocialAuthController extends BaseController
{
    #[OA\Get(
        path: "/api/oauth/google/redirect",
        summary: "Obtenir URL de redirecció a Google",
        description: "Retorna la URL de Google per iniciar el flux OAuth2. El frontend ha de redirigir el navegador a aquesta URL.",
        responses: [
            new OA\Response(
                response: 200,
                description: "URL de Google",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "url", type: "string", example: "https://accounts.google.com/o/oauth2/auth?...")
                    ]
                )
            )
        ],
        tags: ["OAuth"]
    )]
    public function redirect()
    {
        $url = Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    #[OA\Get(
        path: "/api/oauth/google/callback",
        summary: "Callback de Google OAuth2",
        description: "Rep el codi d'autorització de Google, crea/actualitza l'usuari local i retorna un token Sanctum.",
        parameters: [
            new OA\Parameter(name: "code", in: "query", required: true, description: "Codi d'autorització de Google", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "state", in: "query", description: "Paràmetre state per CSRF", schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Login correcte, retorna token Sanctum"),
            new OA\Response(response: 422, description: "Error d'autenticació amb Google")
        ],
        tags: ["OAuth"]
    )]
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al autenticar con Google: ' . $e->getMessage(),
            ], 422);
        }

        // Find or create the local user
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Link Google account if not already linked
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            // Create new user from Google data
            $nameParts = explode(' ', $googleUser->getName(), 2);
            $user = User::create([
                'nombre'         => $nameParts[0],
                'apellidos'      => $nameParts[1] ?? '',
                'nombre_usuario' => explode('@', $googleUser->getEmail())[0],
                'email'          => $googleUser->getEmail(),
                'google_id'      => $googleUser->getId(),
                // contraseña is nullable for OAuth users
            ]);
        }

        // Issue a Sanctum token (same format as normal login)
        $token = $user->createToken('api-google')->plainTextToken;

        return $this->sendResponse([
            'token' => $token,
            'name'  => $user->nombre,
            'email' => $user->email,
            'role'  => $user->role,
            'roles' => $user->roles->pluck('name'),
        ], 'Login con Google exitoso', 200);
    }
}
