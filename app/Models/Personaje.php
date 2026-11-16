<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'altura', 'masa', 'color_cabello', 'color_ojos', 'genero', 'planeta_origen'])]
class Personaje extends Model
{
    protected $table = 'personajes';

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
