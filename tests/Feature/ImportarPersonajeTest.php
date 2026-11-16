<?php

namespace Tests\Feature;

use App\Models\Personaje;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use LogicException;
use PDOException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImportarPersonajeTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            throw new LogicException('Estas pruebas requieren SQLite en memoria para proteger los datos locales.');
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    private function respuestaSwapi(array $cambios = []): array
    {
        return array_replace([
            'name' => 'Luke Skywalker',
            'height' => '172',
            'mass' => '77',
            'hair_color' => 'blond',
            'eye_color' => 'blue',
            'gender' => 'male',
            'homeworld' => 'https://swapi.py4e.com/api/planets/1/',
        ], $cambios);
    }

    public function test_import_maps_fields_and_persists_only_the_requested_character(): void
    {
        Http::fake(['https://swapi.py4e.com/api/people/1/' => Http::response($this->respuestaSwapi())]);

        $this->post(route('personajes.importar'), [
            'swapi_id' => 1, 'nombre' => 'Nombre enviado por el cliente', 'origen_swapi' => false,
        ])->assertRedirect(route('personajes.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('inertia.flash_data.exito', 'Luke Skywalker se importó correctamente desde SWAPI.');

        $this->assertDatabaseCount('personajes', 1);
        $this->assertDatabaseHas('personajes', [
            'nombre' => 'Luke Skywalker', 'altura' => 172, 'masa' => 77,
            'color_cabello' => 'blond', 'color_ojos' => 'blue', 'genero' => 'masculino',
            'planeta_origen' => null, 'origen_swapi' => true, 'swapi_id' => 1,
        ]);
        Http::assertSent(fn (Request $request) => $request->url() === 'https://swapi.py4e.com/api/people/1/' && $request->method() === 'GET');
        Http::assertSentCount(1);
    }

    #[DataProvider('idsInvalidos')]
    public function test_invalid_ids_do_not_call_swapi(mixed $id): void
    {
        Http::fake();
        $this->post(route('personajes.importar'), ['swapi_id' => $id])->assertSessionHasErrors('swapi_id');
        $this->assertDatabaseCount('personajes', 0);
        Http::assertNothingSent();
    }

    public static function idsInvalidos(): array
    {
        return [[null], [''], ['texto'], [0], [-1], [84], [1.5], [[]]];
    }

    public function test_upper_id_boundary_is_allowed(): void
    {
        Http::fake(['https://swapi.py4e.com/api/people/83/' => Http::response($this->respuestaSwapi(['name' => 'Tion Medon']))]);
        $this->post(route('personajes.importar'), ['swapi_id' => '83'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', ['swapi_id' => 83, 'nombre' => 'Tion Medon']);
    }

    public function test_duplicate_id_keeps_the_existing_record_without_calling_swapi(): void
    {
        $personaje = new Personaje(['nombre' => 'Luke editado', 'genero' => 'masculino']);
        $personaje->swapi_id = 1;
        $personaje->origen_swapi = true;
        $personaje->save();
        Http::fake();

        $this->post(route('personajes.importar'), ['swapi_id' => 1])
            ->assertSessionHasErrors(['swapi_id' => 'Este personaje de SWAPI ya está en tu archivo.']);

        $this->assertDatabaseCount('personajes', 1);
        $this->assertDatabaseHas('personajes', ['id' => $personaje->id, 'nombre' => 'Luke editado']);
        Http::assertNothingSent();
    }

    public function test_unique_constraint_handles_an_import_that_arrives_during_the_api_call(): void
    {
        Http::fake(function () {
            $personaje = new Personaje(['nombre' => 'Luke concurrente', 'genero' => 'masculino']);
            $personaje->swapi_id = 1;
            $personaje->origen_swapi = true;
            $personaje->save();

            return Http::response($this->respuestaSwapi());
        });

        $this->post(route('personajes.importar'), ['swapi_id' => 1])
            ->assertSessionHasErrors(['swapi_id' => 'Este personaje de SWAPI ya está en tu archivo.']);
        $this->assertDatabaseCount('personajes', 1);
        $this->assertDatabaseHas('personajes', ['nombre' => 'Luke concurrente', 'swapi_id' => 1]);
    }

    #[DataProvider('estadosHttp')]
    public function test_http_failures_return_a_useful_message_without_saving(int $estado, string $mensaje): void
    {
        Http::fake(['*' => Http::response(['detail' => 'Fallo simulado'], $estado)]);
        $this->from(route('personajes.index'))->post(route('personajes.importar'), ['swapi_id' => 1])
            ->assertRedirect(route('personajes.index'))
            ->assertSessionHasErrors(['swapi_id' => $mensaje]);
        $this->assertDatabaseCount('personajes', 0);
    }

    public static function estadosHttp(): array
    {
        $indisponible = 'SWAPI no está disponible en este momento. Intentá nuevamente más tarde.';

        return [
            [404, 'No encontramos un personaje con ese ID en SWAPI. Probá con otro.'],
            [500, $indisponible], [429, $indisponible], [302, $indisponible],
        ];
    }

    public function test_timeout_or_connection_failure_does_not_save_and_allows_retry(): void
    {
        Http::fake(['*' => Http::sequence()
            ->pushFailedConnection('cURL error 28: Operation timed out')
            ->push($this->respuestaSwapi())]);
        $this->post(route('personajes.importar'), ['swapi_id' => 1])
            ->assertSessionHasErrors(['swapi_id' => 'SWAPI no respondió a tiempo o no está disponible. Intentá nuevamente.']);
        $this->assertDatabaseCount('personajes', 0);

        $this->post(route('personajes.importar'), ['swapi_id' => 1])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('personajes', 1);
    }

    #[DataProvider('respuestasInvalidas')]
    public function test_invalid_api_data_is_not_persisted(mixed $datos): void
    {
        Http::fake(['*' => Http::response($datos)]);
        $this->post(route('personajes.importar'), ['swapi_id' => 1])
            ->assertSessionHasErrors(['swapi_id' => 'SWAPI devolvió datos incompletos o inválidos. No se guardó el personaje.']);
        $this->assertDatabaseCount('personajes', 0);
    }

    public static function respuestasInvalidas(): array
    {
        $base = ['name' => 'Luke Skywalker', 'height' => '172', 'mass' => '77', 'hair_color' => 'blond', 'eye_color' => 'blue', 'gender' => 'male'];

        return [
            ['<html>Error del proveedor</html>'],
            [[]],
            [['name' => 'Luke Skywalker']],
            [array_replace($base, ['name' => str_repeat('a', 101)])],
            [array_replace($base, ['name' => '  a  '])],
            [array_replace($base, ['height' => ['172']])],
        ];
    }

    #[DataProvider('valoresNormalizados')]
    public function test_optional_values_and_gender_follow_the_documented_policy(array $api, array $guardado): void
    {
        Http::fake(['*' => Http::response($this->respuestaSwapi($api))]);
        $this->post(route('personajes.importar'), ['swapi_id' => 1])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', array_merge(['swapi_id' => 1], $guardado));
    }

    public static function valoresNormalizados(): array
    {
        return [
            [['height' => 'unknown', 'mass' => 'unknown', 'hair_color' => 'unknown', 'eye_color' => null, 'gender' => 'unknown'], ['altura' => null, 'masa' => null, 'color_cabello' => null, 'color_ojos' => null, 'genero' => 'n/a']],
            [['height' => '165.9', 'mass' => '49.5', 'gender' => 'female'], ['altura' => 165, 'masa' => 49, 'genero' => 'femenino']],
            [['height' => '301', 'mass' => '1,358', 'gender' => 'hermaphrodite'], ['altura' => null, 'masa' => null, 'genero' => 'n/a']],
            [['height' => '0', 'mass' => '-5', 'gender' => 'none'], ['altura' => null, 'masa' => null, 'genero' => 'n/a']],
            [['height' => '300', 'mass' => '500', 'gender' => 'n/a'], ['altura' => 300, 'masa' => 500, 'genero' => 'n/a']],
        ];
    }

    public function test_imported_droid_can_be_edited_without_losing_its_origin(): void
    {
        Http::fake(['*' => Http::response($this->respuestaSwapi(['name' => 'R2-D2', 'gender' => 'n/a']))]);
        $this->post(route('personajes.importar'), ['swapi_id' => 3])->assertSessionHasNoErrors();
        $personaje = Personaje::sole();

        $this->put(route('personajes.update', $personaje), [
            'nombre' => 'R2-D2', 'genero' => 'n/a', 'altura' => 96, 'masa' => 32,
            'origen_swapi' => false, 'swapi_id' => 2,
        ])->assertRedirect(route('personajes.index'))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', [
            'id' => $personaje->id, 'nombre' => 'R2-D2', 'altura' => 96, 'masa' => 32, 'origen_swapi' => true, 'swapi_id' => 3,
        ]);
    }

    public function test_manual_characters_accept_numbers_and_ignore_a_forged_origin(): void
    {
        $this->post(route('personajes.store'), ['nombre' => 'C-3PO', 'genero' => 'n/a', 'origen_swapi' => true, 'swapi_id' => 2])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('personajes', ['nombre' => 'C-3PO', 'origen_swapi' => false, 'swapi_id' => null]);
    }

    public function test_database_failure_returns_an_error_without_saving(): void
    {
        Http::fake(['*' => Http::response($this->respuestaSwapi())]);
        Personaje::creating(function (): void {
            throw new QueryException('sqlite', 'insert into personajes', [], new PDOException('Fallo simulado'));
        });
        $this->post(route('personajes.importar'), ['swapi_id' => 1])
            ->assertSessionHasErrors(['swapi_id' => 'No pudimos guardar el personaje. Intentá nuevamente.']);
        $this->assertDatabaseCount('personajes', 0);
    }
}
