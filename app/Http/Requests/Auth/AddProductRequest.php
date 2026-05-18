<?php
namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\CustomValidationResponse;

class AddProductRequest extends FormRequest {

    use CustomValidationResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() {

        $deportesValidos = ["trail", "basket", ""]

        return [
            'marca' => '',
            'categoria' => '',
            'nombre' => "",
            'precio' => "", 
            'ajuste' => "", 
            'sexo' => "", 
            'descripcion' => "", 
            'altura' => "", 
            'deporte' => "", 
            'oferta' => "boolean", 
            'novedad' => "boolean", 
            'img' => "",
            "variaciones" => "array"
        ];

        
    }

}