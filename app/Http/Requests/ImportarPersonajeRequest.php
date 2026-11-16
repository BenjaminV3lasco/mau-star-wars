<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportarPersonajeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'swapi_id' => ['bail', 'required', 'integer', 'between:1,83', Rule::unique('personajes', 'swapi_id')],
        ];
    }

    public function messages(): array
    {
        return [
            'swapi_id.required' => 'Ingresá el ID del personaje que querés importar.',
            'swapi_id.integer' => 'El ID debe ser un número entero.',
            'swapi_id.between' => 'El ID debe estar entre 1 y 83.',
            'swapi_id.unique' => 'Este personaje de SWAPI ya está en tu archivo.',
        ];
    }
}
