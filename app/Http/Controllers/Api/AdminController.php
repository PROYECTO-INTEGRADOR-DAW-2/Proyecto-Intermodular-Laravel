<?php
namespace App\Http\Controllers\Api;

use App\Enums\TallaCategoria;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;

use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Requests\Auth\CreateUserRequest;

use App\Http\Requests\Auth\AddRoleRequest;
use App\Http\Requests\Auth\UpdateRoleRequest;

use App\Http\Requests\Auth\AddProductRequest;
use App\Http\Requests\Auth\UpdateProductRequest;
use App\Http\Resources\SizeResource;
use App\Models\Talla;
use Illuminate\Http\Request;


class AdminController extends BaseController {
    

    public function getAllUsers() {
        $allUsers = User::all();

        return $this->sendResponse(UserResource::collection($allUsers), "Se han obtenido correctamente los usuarios", 200);
    }

    public function getAllRoles() {
        $allRoles = Role::all();

        return $this->sendResponse($allRoles, "Se han obtenido los roles del sistema", 200);
    }

    public function addRole(AddRoleRequest $request) {
        $validatedData = $request->validated();

        Role::create($validatedData);
    }

    public function updateRole(Role $role, UpdateRoleRequest $request) {
        $validatedData = $request->validated();

        $role->update($validatedData);
    }

    public function deleteRole(Role $role) {
        $role->delete();
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

    public function updateProduct(Product $product, UpdateProductRequest $request) {
        $validated = $request->validated();

        $product->update($validated);

        return $this->sendMessage("Se ha actualizado correctamente el producto", 200);

    }

    public function createProduct(AddProductRequest $request) {
        $validated = $request->validated();

        Product::create($validated);

        return $this->sendMessage("Se ha añadido correctamente el producto", 200);
    }

    public function deleteProduct(Product $product) {
        $product->delete();
    }

    public function getSizes(Request $request) {

        $category = $request->input("category");
        $gender = $request->input("gender");

        $categoriaEnum = TallaCategoria::tryFrom($category);

        $correctValidation = ['adulto' => ['mujer', 'hombre'], 'infantil' => ['niño', 'niña']];


        if ($category && $gender && $categoriaEnum) {

            
            // Determinamos si es adulto o infantil buscando la palabra en la categoría
            $lowerCategory = strtolower($category);
            $type = str_contains($lowerCategory, 'adulto') ? 'adulto' : 'infantil';

            // Validamos si el género es compatible con el tipo de talla (Adulto/Infantil)
            if (!in_array(strtolower($gender), $correctValidation[$type])) {
                return $this->sendError('La categoria de talla no coincide con el sexo del producto', [], 400);
            }
        
            $sizes = Talla::query()->where([
                ['categoria', '=', $categoriaEnum->value], 
                ['genero', '=', $gender]]
            )->get();

            $sizes = SizeResource::collection($sizes);

            return $this->sendResponse($sizes, "Se han obtenido las tallas disponibles", 200);
        } else {
            return $this->sendError('La categoria buscada no existe', [], 404);
        }

    }



}