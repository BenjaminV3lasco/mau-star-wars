<?php

namespace Tests\Feature;

use App\Models\Personaje;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PersonajeAceptacionTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            throw new LogicException('Estas pruebas requieren SQLite en memoria para proteger los datos locales.');
        }
    }

    public function test_schema_has_all_required_columns_and_unique_swapi_id(): void
    {
        $this->assertEqualsCanonicalizing([
            'id', 'nombre', 'altura', 'masa', 'color_cabello', 'color_ojos', 'genero',
            'planeta_origen', 'origen_swapi', 'swapi_id', 'created_at', 'updated_at',
        ], Schema::getColumnListing('personajes'));

        Personaje::create(['nombre' => 'Leia Organa', 'genero' => 'femenino']);
        Personaje::create(['nombre' => 'Luke Skywalker', 'genero' => 'masculino']);
        $this->assertDatabaseCount('personajes', 2);
        $this->assertDatabaseHas('personajes', ['nombre' => 'Leia Organa', 'origen_swapi' => false, 'swapi_id' => null]);

        $primero = new Personaje(['nombre' => 'R2-D2', 'genero' => 'n/a']);
        $primero->swapi_id = 3;
        $primero->origen_swapi = true;
        $primero->save();
        $this->expectException(UniqueConstraintViolationException::class);
        $repetido = new Personaje(['nombre' => 'Otro droide', 'genero' => 'n/a']);
        $repetido->swapi_id = 3;
        $repetido->save();
    }

    public function test_list_shows_ten_characters_per_page_and_remaining_character_on_second_page(): void
    {
        for ($numero = 1; $numero <= 11; $numero++) {
            Personaje::create(['nombre' => 'Personaje '.str_pad((string) $numero, 2, '0', STR_PAD_LEFT), 'genero' => 'n/a']);
        }

        $this->get(route('personajes.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Personajes/Index')
                ->has('personajes.data', 10)
                ->where('personajes.total', 11)
                ->where('personajes.current_page', 1));
        $this->get(route('personajes.index', ['page' => 2]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Personajes/Index')
                ->has('personajes.data', 1)
                ->where('personajes.total', 11)
                ->where('personajes.current_page', 2));
    }

    public function test_create_and_edit_persist_valid_data_and_keep_origin_manual(): void
    {
        $this->post(route('personajes.store'), [
            'nombre' => 'Obi Wan Kenobi', 'altura' => '', 'masa' => '',
            'genero' => 'masculino', 'planeta_origen' => 'Stewjon',
            'origen_swapi' => true, 'swapi_id' => 1,
        ])->assertRedirect(route('personajes.index'))->assertSessionHasNoErrors();
        $personaje = Personaje::sole();
        $this->assertNull($personaje->altura);
        $this->assertNull($personaje->masa);
        $this->assertFalse($personaje->origen_swapi);
        $this->assertNull($personaje->swapi_id);

        $this->get(route('personajes.edit', $personaje))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Personajes/Editar')
                ->where('personaje.nombre', 'Obi Wan Kenobi'));
        $this->put(route('personajes.update', $personaje), [
            'nombre' => 'Obi Wan Kenobi', 'altura' => 182, 'masa' => 77,
            'genero' => 'masculino', 'color_ojos' => 'Azul',
        ])->assertRedirect(route('personajes.index'))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personajes', [
            'id' => $personaje->id, 'altura' => 182, 'masa' => 77,
            'color_ojos' => 'Azul', 'origen_swapi' => false, 'swapi_id' => null,
        ]);
    }

    #[DataProvider('entradasInvalidas')]
    public function test_manual_form_rejects_invalid_values(array $cambios, string $campo): void
    {
        $this->post(route('personajes.store'), array_replace([
            'nombre' => 'Leia Organa', 'genero' => 'femenino',
        ], $cambios))->assertSessionHasErrors($campo);
        $this->assertDatabaseCount('personajes', 0);
    }

    public static function entradasInvalidas(): array
    {
        return [
            [['nombre' => ''], 'nombre'],
            [['nombre' => 'ab'], 'nombre'],
            [['nombre' => str_repeat('a', 101)], 'nombre'],
            [['nombre' => 'Leia 2'], 'nombre'],
            [['altura' => 0], 'altura'],
            [['altura' => 301], 'altura'],
            [['altura' => 1.5], 'altura'],
            [['masa' => 0], 'masa'],
            [['masa' => 501], 'masa'],
            [['masa' => 1.5], 'masa'],
            [['genero' => 'other'], 'genero'],
            [['color_cabello' => str_repeat('a', 101)], 'color_cabello'],
            [['color_ojos' => str_repeat('a', 101)], 'color_ojos'],
            [['planeta_origen' => str_repeat('a', 101)], 'planeta_origen'],
        ];
    }

    public function test_boundary_values_and_nullable_fields_are_accepted(): void
    {
        $this->post(route('personajes.store'), [
            'nombre' => str_repeat('a', 100), 'altura' => 300, 'masa' => 500,
            'genero' => 'n/a', 'color_cabello' => str_repeat('b', 100),
            'color_ojos' => null, 'planeta_origen' => 'Yavin 4',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('personajes', [
            'nombre' => str_repeat('a', 100), 'altura' => 300, 'masa' => 500,
            'color_cabello' => str_repeat('b', 100), 'color_ojos' => null,
            'planeta_origen' => 'Yavin 4',
        ]);
    }
}
