<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SwapiService
{
    private const URL_PERSONAJES = 'https://swapi.py4e.com/api/people/';

    public function obtenerPersonaje(int $id): array
    {
        $datos = $this->consultar($id)->json();

        if (! $this->sonDatosValidos($datos)) {
            $this->fallar('SWAPI devolvió datos incompletos o inválidos. No se guardó el personaje.');
        }

        return $this->traducir($datos);
    }

    private function consultar(int $id): Response
    {
        try {
            $respuesta = Http::acceptJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->withoutRedirecting()
                ->get(self::URL_PERSONAJES.$id.'/');
        } catch (ConnectionException) {
            $this->fallar('SWAPI no respondió a tiempo o no está disponible. Intentá nuevamente.');
        }

        if ($respuesta->notFound()) {
            $this->fallar('No encontramos un personaje con ese ID en SWAPI. Probá con otro.');
        }

        if (! $respuesta->ok()) {
            $this->fallar('SWAPI no está disponible en este momento. Intentá nuevamente más tarde.');
        }

        return $respuesta;
    }

    private function sonDatosValidos(mixed $datos): bool
    {
        if (! is_array($datos)) {
            return false;
        }

        if (is_string($datos['name'] ?? null)) {
            $datos['name'] = trim($datos['name']);
        }

        return Validator::make($datos, [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'height' => ['present', 'nullable', 'string', 'max:100'],
            'mass' => ['present', 'nullable', 'string', 'max:100'],
            'hair_color' => ['present', 'nullable', 'string', 'max:100'],
            'eye_color' => ['present', 'nullable', 'string', 'max:100'],
            'gender' => ['required', 'string', 'max:100'],
        ])->passes();
    }

    private function traducir(array $datos): array
    {
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

        // SWAPI usa coma para miles ("1,358") y punto para decimales.
        if (! preg_match('/^\d+(?:,\d{3})*(?:\.\d+)?$/', $valor)) {
            return null;
        }

        $numero = (float) str_replace(',', '', $valor);

        return $numero >= 1 && $numero <= $maximo ? (int) $numero : null;
    }

    private function textoOpcional(?string $valor): ?string
    {
        $valor = trim($valor ?? '');

        return $valor === '' || strtolower($valor) === 'unknown' ? null : $valor;
    }

    private function fallar(string $mensaje): never
    {
        throw ValidationException::withMessages(['swapi_id' => $mensaje]);
    }
}
