<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\CustomValidationResponse; 

class UpdateUserRequest extends FormRequest
{
    use CustomValidationResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rolesValidos = ['admin','client'];
        $userId = $this->route('user')->id;

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'nombre_usuario' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($userId)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'rol' => ['required', Rule::in($rolesValidos)]
        ];
    }
}
