<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Traits\CustomValidationResponse; 
use Illuminate\Validation\Rules\Password;

class PasswordUpdateRequest extends FormRequest
{
    use CustomValidationResponse;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contraseña-actual' => ['required', 'current_password'],
            'contraseña-nueva' => ['required', Password::defaults() ]
        ];
    }
}
