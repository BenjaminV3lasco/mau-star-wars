<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SwapiService
{
    public function obtenerPersonaje(int $id): array
    {
        try {
            $response = Http::acceptJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->withoutRedirecting()
                ->get('https://swapi.py4e.com/api/people/'.$id.'/');
        } catch (ConnectionException) {
            throw ValidationException::withMessages([
                'swapi_id' => 'SWAPI no respondió a tiempo o no está disponible. Intentá nuevamente.',
            ]);
        }

        if ($response->notFound()) {
            throw ValidationException::withMessages([
                'swapi_id' => 'No encontramos un personaje con ese ID en SWAPI. Probá con otro.',
            ]);
        }

        if (! $response->ok()) {
            throw ValidationException::withMessages([
                'swapi_id' => 'SWAPI no está disponible en este momento. Intentá nuevamente más tarde.',
            ]);
        }

        $datos = $response->json();

        if (is_array($datos) && isset($datos['name']) && is_string($datos['name'])) {
            $datos['name'] = trim($datos['name']);
        }

        if (! is_array($datos) || Validator::make($datos, [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'height' => ['present', 'nullable', 'string', 'max:100'],
            'mass' => ['present', 'nullable', 'string', 'max:100'],
            'hair_color' => ['present', 'nullable', 'string', 'max:100', 'not_regex:/\p{N}/u'],
            'eye_color' => ['present', 'nullable', 'string', 'max:100', 'not_regex:/\p{N}/u'],
            'gender' => ['required', 'string', 'max:100'],
        ])->fails()) {
            throw ValidationException::withMessages([
                'swapi_id' => 'SWAPI devolvió datos incompletos o inválidos. No se guardó el personaje.',
            ]);
        }

        return [
            'nombre' => trim($datos['name']),
            'altura' => $this->medida($datos['height'], 300),
            'masa' => $this->medida($datos['mass'], 500),
            'color_cabello' => $this->textoOpcional($datos['hair_color']),
            'color_ojos' => $this->textoOpcional($datos['eye_color']),
            'genero' => match (strtolower(trim($datos['gender']))) {
                'male' => 'masculino',
                'female' => 'femenino',
                default => 'n/a',
            },
            'planeta_origen' => null,
        ];
    }

    private function medida(?string $valor, int $maximo): ?int
    {
        $valor = trim($valor ?? '');

        // SWAPI usa comas para miles y puntos para decimales.
        if (! preg_match('/^\d+(?:,\d{3})*(?:\.\d+)?$/', $valor)) {
            return null;
        }

        $numero = (float) str_replace(',', '', $valor);

        // Un dato fuera de los límites queda vacío, sin inventar otro valor.
        return $numero >= 1 && $numero <= $maximo ? (int) $numero : null;
    }

    private function textoOpcional(?string $valor): ?string
    {
        $valor = trim($valor ?? '');

        return $valor === '' || strtolower($valor) === 'unknown' ? null : $valor;
    }
}
