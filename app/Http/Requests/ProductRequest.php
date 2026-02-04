<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255'],
            'marca' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:255'],
            'talla' => ['required', 'string', 'max:255'],
            'sexo' => ['required', 'string', 'max:255'],
            'altura' => ['required', 'string', 'max:255'],
            'deporte' => ['required', 'string', 'max:255'],
            'oferta' => ['required', 'boolean', 'max:255'],
            'categoria' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'decimal'],
            'stock' => ['required', 'integer'],
            'categoria' => ['required', 'string'],
            'imagen' => ['required', 'string'],
        ];
    }
}
