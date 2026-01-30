<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class ImportProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Pillamos la primera clave que sea un archivo
        $fileKey = collect($this->allFiles())->keys()->first();

        // Si no hay archivos, Laravel fallará en el 'required' de abajo
        if (!$fileKey) {
            return ['file' => 'required']; 
        }

        return [
            $fileKey => [
                'required',
                'file',
                'mimes:csv,xlsx,xls',
                'max:2048',
            ],
        ];
    }

    public function getUploadedFile(): ?UploadedFile
    {
        // No buscamos por nombre, pillamos el primer archivo que exista en la request
        return collect($this->allFiles())->first();
    }
}