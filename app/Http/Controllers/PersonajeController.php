<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarPersonajeRequest;
use App\Http\Requests\ImportarPersonajeRequest;
use App\Models\Personaje;
use App\Services\SwapiService;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PersonajeController extends Controller
{
    public function index(Request $request): Response
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'genero' => ['nullable', 'in:masculino,femenino,n/a'],
            'orden' => ['nullable', 'in:id,nombre,altura,masa,genero'],
            'direccion' => ['nullable', 'in:asc,desc'],
        ]);

        $buscar = trim($filtros['buscar'] ?? '');
        $genero = $filtros['genero'] ?? '';
        $orden = $filtros['orden'] ?? 'id';
        $direccion = $filtros['direccion'] ?? 'desc';
        $ordenaPorMedida = in_array($orden, ['altura', 'masa'], true);

        $personajes = Personaje::query()
            ->select(['id', 'nombre', 'altura', 'masa', 'genero', 'origen_swapi'])
            ->when($buscar !== '', fn ($query) => $query->where('nombre', 'like', "%{$buscar}%"))
            ->when($genero !== '', fn ($query) => $query->where('genero', $genero))
            ->when($ordenaPorMedida, fn ($query) => $query->orderByRaw("{$orden} IS NULL"))
            ->orderBy($orden, $direccion)
            ->when($orden !== 'id', fn ($query) => $query->orderBy('id'))
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Personajes/Index', [
            'personajes' => $personajes,
            'filtros' => compact('buscar', 'genero', 'orden', 'direccion'),
        ]);
    }

    public function show(Personaje $personaje): Response
    {
        return Inertia::render('Personajes/Mostrar', [
            'personaje' => $personaje,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Personajes/Crear');
    }

    public function store(GuardarPersonajeRequest $request): RedirectResponse
    {
        $personaje = Personaje::create($request->validated());

        Inertia::flash('exito', $personaje->nombre.' se sumó al archivo.');

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

        Inertia::flash('exito', $personaje->nombre.' llegó desde SWAPI y ya forma parte del archivo.');

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

        Inertia::flash('exito', 'La ficha de '.$personaje->nombre.' se actualizó.');

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

        Inertia::flash('exito', $personaje->nombre.' fue eliminado del archivo.');

        return to_route('personajes.index');
    }
}
