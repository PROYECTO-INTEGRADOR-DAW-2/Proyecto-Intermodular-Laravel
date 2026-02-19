<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'sku'         => ['nullable', 'string', 'max:255'],
            'marca'       => ['nullable', 'string', 'max:255'],
            'color'       => ['nullable', 'string', 'max:255'],
            'talla'       => ['nullable', 'string', 'max:255'],
            'sexo'        => ['nullable', 'string', 'max:255'],
            'altura'      => ['nullable', 'string', 'max:255'],
            'deporte'     => ['nullable', 'string', 'max:255'],
            'oferta'      => ['nullable', 'boolean'],
            'categoria'   => ['nullable', 'string', 'max:255'],
            'precio'      => ['required', 'numeric', 'min:0'],
            'stock'       => ['nullable', 'integer', 'min:0'],
            'img'         => ['nullable', 'string'],
            'ajuste'      => ['nullable', 'string', 'max:255'],
        ];
    }
}
