<?php

namespace App\Http\Controllers;

use App\Models\Personaje;
use Inertia\Inertia;
use Inertia\Response;

class PersonajeController extends Controller
{
    public function index(): Response
    {
        $personajes = Personaje::query()
            ->select(['id', 'nombre', 'altura', 'masa', 'genero', 'origen_swapi'])
            ->orderByDesc('id')
            ->paginate(10);

        return Inertia::render('Personajes/Index', [
            'personajes' => $personajes,
        ]);
    }
}
