<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarPersonajeRequest;
use App\Http\Requests\ImportarPersonajeRequest;
use App\Models\Personaje;
use App\Services\SwapiService;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
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

    public function importar(ImportarPersonajeRequest $request, SwapiService $swapi): RedirectResponse
    {
        $id = (int) $request->validated('swapi_id');
        $personaje = new Personaje($swapi->obtenerPersonaje($id));
        $personaje->origen_swapi = true;
        $personaje->swapi_id = $id;

        try {
            $personaje->save();
        } catch (UniqueConstraintViolationException) {
            return back()->withErrors([
                'swapi_id' => 'Este personaje de SWAPI ya está en tu archivo.',
            ]);
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors([
                'swapi_id' => 'No pudimos guardar el personaje. Intentá nuevamente.',
            ]);
        }

        Inertia::flash('exito', $personaje->nombre.' se importó correctamente desde SWAPI.');

        return to_route('personajes.index');
    }

    public function edit(Personaje $personaje): Response
    {
        return Inertia::render('Personajes/Editar', [
            'personaje' => $personaje,
        ]);
    }

    public function update(GuardarPersonajeRequest $request, Personaje $personaje): RedirectResponse
    {
        $personaje->update($request->validated());

        Inertia::flash('exito', 'Personaje actualizado correctamente.');

        return to_route('personajes.index');
    }

    public function destroy(Personaje $personaje): RedirectResponse
    {
        try {
            $personaje->delete();
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors([
                'eliminar' => 'No pudimos eliminar el personaje. Intentá nuevamente.',
            ]);
        }

        Inertia::flash('exito', 'Personaje eliminado correctamente.');

        return to_route('personajes.index');
    }
}
