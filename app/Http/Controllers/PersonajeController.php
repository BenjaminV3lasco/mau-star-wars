<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarPersonajeRequest;
use App\Models\Personaje;
use Illuminate\Http\RedirectResponse;
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

    public function create(): Response
    {
        return Inertia::render('Personajes/Crear');
    }

    public function store(GuardarPersonajeRequest $request): RedirectResponse
    {
        Personaje::create($request->validated());

        Inertia::flash('exito', 'Personaje creado correctamente.');

        return to_route('personajes.index');
    }
}
