<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Consulta un personaje en SWAPI y lo traduce al formato de la tabla personajes.
 *
 * Toda falla (timeout, 404, error del servidor o datos incompletos) se lanza
 * como ValidationException sobre el campo swapi_id. Así Laravel la devuelve
 * al modal igual que cualquier error de validación, sin lógica extra en el
 * controlador ni en Vue.
 */
class SwapiService
{
    private const URL_PERSONAJES = 'https://swapi.py4e.com/api/people/';

    private const SEGUNDOS_PARA_CONECTAR = 5;

    private const SEGUNDOS_PARA_RESPONDER = 15;

    /**
     * Devuelve los datos listos para crear un Personaje.
     */
    public function obtenerPersonaje(int $id): array
    {
        $datos = $this->consultar($id)->json();

        if (! $this->sonDatosValidos($datos)) {
            $this->fallar('SWAPI devolvió datos incompletos o inválidos. No se guardó el personaje.');
        }

        return $this->traducir($datos);
    }

    /**
     * Hace la petición HTTP y traduce cada falla a un mensaje para el usuario.
     *
     * Los timeouts evitan que la importación quede colgada si la API está
     * caída. No se siguen redirecciones: una respuesta distinta de 200 se
     * trata como error.
     */
    private function consultar(int $id): Response
    {
        try {
            $respuesta = Http::acceptJson()
                ->connectTimeout(self::SEGUNDOS_PARA_CONECTAR)
                ->timeout(self::SEGUNDOS_PARA_RESPONDER)
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

    /**
     * La respuesta de una API externa no es confiable: se exige que traiga
     * los campos esperados con el tipo correcto antes de guardar nada.
     */
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

    /**
     * Mapea los campos en inglés a los de la consigna.
     *
     * Los géneros que no son male ni female (hermaphrodite, none, n/a) se
     * guardan como n/a, el único otro valor que admite la tabla. planeta_origen
     * queda vacío porque SWAPI sólo entrega la URL del planeta en homeworld.
     */
    private function traducir(array $datos): array
    {
        return [
            'nombre' => trim($datos['name']),
            'altura' => self::convertirMedida($datos['height'], 300),
            'masa' => self::convertirMedida($datos['mass'], 500),
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

    /**
     * Convierte una medida de SWAPI a entero dentro de los límites del formulario.
     *
     * SWAPI usa coma para miles ("1,358") y punto para decimales ("78.2"), y
     * "unknown" cuando no tiene el dato. Un valor desconocido o fuera de
     * límites queda vacío en lugar de inventar uno; así un personaje importado
     * sigue siendo editable con las mismas reglas que uno manual.
     */
    public static function convertirMedida(?string $valor, int $maximo): ?int
    {
        $valor = trim($valor ?? '');

        if (! preg_match('/^\d+(?:,\d{3})*(?:\.\d+)?$/', $valor)) {
            return null;
        }

        $numero = (float) str_replace(',', '', $valor);

        return $numero >= 1 && $numero <= $maximo ? (int) $numero : null;
    }

    /**
     * SWAPI escribe "unknown" cuando no tiene el dato; se guarda vacío.
     */
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
