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

/**
 * Coordina las pantallas y acciones de personajes.
 *
 * El controlador se mantiene corto a propósito: la validación vive en los
 * Form Requests y la integración con SWAPI en SwapiService. Acá sólo se
 * decide qué página renderizar o a dónde redirigir.
 */
class PersonajeController extends Controller
{
    private const POR_PAGINA = 10;

    /**
     * Listado paginado con búsqueda por nombre, filtro por género y orden.
     *
     * Los filtros se validan contra listas cerradas, así que la columna de
     * orden nunca llega a la consulta con un valor arbitrario. Todo se aplica
     * en la base antes de paginar, para que las 10 fichas de cada página
     * respeten el filtro y el orden elegidos.
     */
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
            // Las medidas vacías quedan al final en ambas direcciones.
            ->when($ordenaPorMedida, fn ($query) => $query->orderByRaw("{$orden} IS NULL"))
            ->orderBy($orden, $direccion)
            // Desempate estable para que la paginación no repita ni saltee fichas.
            ->when($orden !== 'id', fn ($query) => $query->orderBy('id'))
            ->paginate(self::POR_PAGINA)
            ->withQueryString();

        return Inertia::render('Personajes/Index', [
            'personajes' => $personajes,
            'filtros' => compact('buscar', 'genero', 'orden', 'direccion'),
        ]);
    }

    /**
     * Ficha completa de un personaje. El route model binding responde 404
     * por sí solo si el ID no existe.
     */
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

    /**
     * Guarda un personaje manual. Sólo se usan los campos validados, así que
     * origen_swapi y swapi_id no se pueden forzar desde el formulario.
     */
    public function store(GuardarPersonajeRequest $request): RedirectResponse
    {
        $personaje = Personaje::create($request->validated());

        Inertia::flash('exito', $personaje->nombre.' se sumó al archivo.');

        return to_route('personajes.index');
    }

    /**
     * Importa un personaje desde SWAPI por su ID.
     *
     * El Form Request ya descartó IDs fuera de rango o importados antes, y
     * SwapiService convierte cualquier falla de la API en un error del campo.
     * El índice único de swapi_id cubre el caso de dos importaciones del
     * mismo ID que llegan casi al mismo tiempo.
     */
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

    /**
     * Actualiza con las mismas reglas que el alta. El origen del registro
     * (manual o SWAPI) no cambia al editar.
     */
    public function update(GuardarPersonajeRequest $request, Personaje $personaje): RedirectResponse
    {
        $personaje->update($request->validated());

        Inertia::flash('exito', 'La ficha de '.$personaje->nombre.' se actualizó.');

        return to_route('personajes.index');
    }

    /**
     * Elimina el personaje. Si la base falla, se informa en el modal de
     * confirmación en lugar de mostrar una página de error.
     */
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
