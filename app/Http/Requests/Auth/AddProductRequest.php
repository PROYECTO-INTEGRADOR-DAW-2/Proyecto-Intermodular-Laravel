<?php
namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\CustomValidationResponse;

class AddProductRequest extends FormRequest {

    private $configValidation = [
        'ajustes'    => ["ajustado", "holgado","normal"],
        'marcas'     => ["nike", "adidas", "puma", "asics"],
        'alturas'    => ["alto", "bajo", "normal", ""],
        'tallas'     => ["s", "m", "l", "xl"],
        'deportes'   => ["trail", "futbol", "tenis", "padel", "baloncesto"],
        'categorias' => ["zapatillas", "camisetas", "pantalones"],
        'sexos'      => ["hombre", "mujer", "niño", "niña"]
    ];

    use CustomValidationResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules() 
    {
        return [
            'marca' => ['required', 'string', Rule::in($this->configValidation['marcas'])],
            'categoria' => ['required', 'string', Rule::in($this->configValidation['categorias'])],
            'nombre' => ['required', 'string', 'unique:products,nombre'],
            'precio' => ['required', 'decimal:1,2'], 
            'ajuste' => ['required', 'string', Rule::in($this->configValidation['ajustes'])], 
            'sexo' => ['required', 'string', Rule::in($this->configValidation['sexos'])], 
            'descripcion' => ['required', 'string', 'max:255'], 
            'altura' => ['required', 'string', Rule::in($this->configValidation['alturas'])], 
            'deporte' => ['required', 'string', Rule::in($this->configValidation['deportes'])], 
            'oferta' => ['required', 'boolean'], 
            'novedad' => ['required', 'boolean'], 
            'img' => ['required', 'string'],
            "variaciones" => ['required', 'array'],
        ];

        
    }

}