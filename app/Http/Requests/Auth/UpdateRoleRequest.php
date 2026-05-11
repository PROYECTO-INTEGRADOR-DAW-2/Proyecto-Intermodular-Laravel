<?php
namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\CustomValidationResponse;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest {

    use CustomValidationResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() {

        $roleId = $this->route('rol')->id;

        return [
            'rol' => Rule::unique('roles')->ignore($roleId),
            'descripcion' => 'max:255'
        ];
    }

}