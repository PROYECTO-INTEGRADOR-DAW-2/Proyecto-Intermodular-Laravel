<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\CustomValidationResponse;
use Illuminate\Validation\Rules\Password;


class CreateUserRequest extends FormRequest {

    use CustomValidationResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() {

        $rolesValidos = ['admin','client'];
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'nombre_usuario' => ['required', 'string', 'max:255', 'unique:users,nombre_usuario'],
            'contraseña' => ['required', Password::defaults()],
            'confirm_contraseña' => ['required', 'same:contraseña'],
            'email' => ['required', 'email', 'unique:users,email'],
            'rol' => ['required', Rule::in($rolesValidos)]
        ];
    }

}