<?php

namespace App\Http\Requests;

use App\Models\Personaje;
use Illuminate\Foundation\Http\FormRequest;

class GuardarPersonajeRequest extends FormRequest
{
    public function rules(): array
    {
        $personaje = $this->route('personaje');
        $reglasNombre = ['required', 'string', 'min:3', 'max:100'];

        // Los droides importados, como R2-D2, conservan su nombre al editar.
        if (! ($personaje instanceof Personaje && $personaje->origen_swapi)) {
            $reglasNombre[] = 'not_regex:/\p{N}/u';
        }

        return [
            'nombre' => $reglasNombre,
            'altura' => ['nullable', 'integer', 'min:1', 'max:300'],
            'masa' => ['nullable', 'integer', 'min:1', 'max:500'],
            'color_cabello' => ['nullable', 'string', 'max:100', 'not_regex:/\p{N}/u'],
            'color_ojos' => ['nullable', 'string', 'max:100', 'not_regex:/\p{N}/u'],
            'genero' => ['required', 'in:masculino,femenino,n/a'],
            'planeta_origen' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'not_regex' => 'El campo :attribute no puede contener números.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'nombre.min' => 'El nombre debe tener al menos :min caracteres.',
            'nombre.max' => 'El nombre admite hasta :max caracteres.',
            'altura.min' => 'La altura debe ser de al menos :min cm.',
            'altura.max' => 'La altura admite hasta :max cm.',
            'masa.min' => 'La masa debe ser de al menos :min kg.',
            'masa.max' => 'La masa admite hasta :max kg.',
            'color_cabello.max' => 'El color de cabello admite hasta :max caracteres.',
            'color_ojos.max' => 'El color de ojos admite hasta :max caracteres.',
            'planeta_origen.max' => 'El planeta de origen admite hasta :max caracteres.',
            'genero.in' => 'Elegí masculino, femenino o n/a.',
        ];
    }

    public function attributes(): array
    {
        return [
            'color_cabello' => 'color de cabello',
            'color_ojos' => 'color de ojos',
            'genero' => 'género',
            'planeta_origen' => 'planeta de origen',
        ];
    }
}
