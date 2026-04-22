<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\UpdateUserRequest;

class AdminController extends BaseController {

    public function getAllUsers() {
        $allUsers = User::all();

        return $this->sendResponse(UserResource::collection($allUsers), "Se han obtenido correctamente los usuarios", 200);
    }

    public function getAllRoles() {
        $allRoles = ["admin", "client"];

        return $this->sendResponse($allRoles, "Se han obtenido los roles del sistema", 200);
    }

    public function updateUser(User $user, UpdateUserRequest $request) {
        $validated = $request->validated();

        $user->update($validated);

    }



}