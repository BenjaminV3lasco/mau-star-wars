<?php

namespace Database\Seeders;

use App\Models\Personaje;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $personajes = [
            ['nombre' => 'Ahsoka Tano', 'altura' => 170, 'masa' => null, 'color_cabello' => null, 'color_ojos' => 'azul', 'genero' => 'femenino', 'planeta_origen' => 'Shili'],
            ['nombre' => 'Din Djarin', 'altura' => 180, 'masa' => 80, 'color_cabello' => 'castaño', 'color_ojos' => 'marrón', 'genero' => 'masculino', 'planeta_origen' => 'Aq Vetina'],
            ['nombre' => 'Rey', 'altura' => 170, 'masa' => 54, 'color_cabello' => 'castaño', 'color_ojos' => 'avellana', 'genero' => 'femenino', 'planeta_origen' => 'Jakku'],
            ['nombre' => 'Finn', 'altura' => 178, 'masa' => 73, 'color_cabello' => 'negro', 'color_ojos' => 'marrón', 'genero' => 'masculino', 'planeta_origen' => null],
            ['nombre' => 'Poe Dameron', 'altura' => 172, 'masa' => 80, 'color_cabello' => 'castaño', 'color_ojos' => 'marrón', 'genero' => 'masculino', 'planeta_origen' => 'Yavin 4'],
            ['nombre' => 'BB-8', 'altura' => 67, 'masa' => 18, 'color_cabello' => null, 'color_ojos' => 'negro', 'genero' => 'n/a', 'planeta_origen' => null],
            ['nombre' => 'Cassian Andor', 'altura' => 178, 'masa' => 75, 'color_cabello' => 'castaño', 'color_ojos' => 'marrón', 'genero' => 'masculino', 'planeta_origen' => 'Kenari'],
            ['nombre' => 'Jyn Erso', 'altura' => 160, 'masa' => 50, 'color_cabello' => 'castaño', 'color_ojos' => 'verde', 'genero' => 'femenino', 'planeta_origen' => 'Vallt'],
            ['nombre' => 'Grogu', 'altura' => 41, 'masa' => 6, 'color_cabello' => 'blanco', 'color_ojos' => 'marrón', 'genero' => 'masculino', 'planeta_origen' => null],
            ['nombre' => 'Hera Syndulla', 'altura' => 175, 'masa' => 59, 'color_cabello' => null, 'color_ojos' => 'verde', 'genero' => 'femenino', 'planeta_origen' => 'Ryloth'],
            ['nombre' => 'K-2SO', 'altura' => 216, 'masa' => null, 'color_cabello' => null, 'color_ojos' => 'blanco', 'genero' => 'n/a', 'planeta_origen' => null],
            ['nombre' => 'Sabine Wren', 'altura' => 167, 'masa' => 55, 'color_cabello' => 'violeta', 'color_ojos' => 'marrón', 'genero' => 'femenino', 'planeta_origen' => 'Krownest'],
        ];

        foreach ($personajes as $personaje) {
            Personaje::firstOrCreate(['nombre' => $personaje['nombre']], $personaje);
        }
    }
}
