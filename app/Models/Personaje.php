<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Personaje del archivo, creado a mano o importado desde SWAPI.
 *
 * origen_swapi y swapi_id quedan fuera de Fillable: sólo los asigna la
 * importación, nunca los datos que manda un formulario.
 */
#[Fillable(['nombre', 'altura', 'masa', 'color_cabello', 'color_ojos', 'genero', 'planeta_origen'])]
class Personaje extends Model
{
    protected function casts(): array
    {
        return [
            'altura' => 'integer',
            'masa' => 'integer',
            'origen_swapi' => 'boolean',
            'swapi_id' => 'integer',
        ];
    }
}
