<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Reglas de alta y edición de personajes, tal como las pide la consigna.
 *
 * Se valida en el servidor aunque el formulario también valide, porque una
 * petición puede llegar sin pasar por Vue. Crear y editar comparten estas
 * reglas para que un personaje nunca quede en un estado que el otro rechace.
 */
class GuardarPersonajeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'min:3', 'max:100'],
            'altura' => ['nullable', 'integer', 'min:1', 'max:300'],
            'masa' => ['nullable', 'integer', 'min:1', 'max:500'],
            'color_cabello' => ['nullable', 'string', 'max:100'],
            'color_ojos' => ['nullable', 'string', 'max:100'],
            'genero' => ['required', 'in:masculino,femenino,n/a'],
            'planeta_origen' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Mensajes en español y con la unidad de cada medida.
     */
    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
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
