<?php
namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Requests\Auth\CreateUserRequest;

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

        //HARDCODED BUSQUEDA DE EMAILS USADOS
        /* $takenEmails = array_column(
            array_filter(User::all(['id','email'])->toArray(), function ($value) use ($user) {
                $user->id !== $value['id'];
            }
        ), 'email');

        
        if (array_search($user->email, $takenEmails)) {
            return $this->sendError('El email que has especificado esta siendo usado', ['email' => ["El email esta siendo usado"]], 400);
        } else {
            $user->update($validated);
            return $this->sendMessage("Se ha actualizado correctamente el usuario", 200);
        } */

        $user->update($validated);
        return $this->sendMessage("Se ha actualizado correctamente el usuario", 200);

    }

    public function createUser(CreateUserRequest $request) {
        $validated = $request->validated();

        User::create($validated);

        return $this->sendMessage("Se ha añadido correctamente el usuario", 200);

    }

    public function deleteUser(User $user) {
        $user->delete();
    }



}