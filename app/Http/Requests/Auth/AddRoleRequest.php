<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\CustomValidationResponse;

class AddRoleRequest extends FormRequest {

    use CustomValidationResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() {

        return [
            'rol' => 'unique:roles,rol',
            'descripcion' => 'max:255'
        ];
    }

}