<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\PasswordUpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordController extends BaseController
{
    /**
     * Update the user's password.
     */
    public function update(PasswordUpdateRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();

        $user->update([
            'contraseña' => Hash::make($validated['contraseña-nueva']),
        ]);

        $user->tokens()->delete();

        $newToken = $user->createToken('api')->plainTextToken;
    
        $payload = [
            'token' => $newToken,
        ];

        $this->sendResponse($payload, "Se ha actualizado correctamente la contraseña", 200);
        
    }
}
