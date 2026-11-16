<?php

namespace Tests\Feature;

use App\Models\Personaje;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use PDOException;
use Tests\TestCase;

class EliminarPersonajeTest extends TestCase
{
    use RefreshDatabase;

    protected function beforeRefreshingDatabase(): void
    {
        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            throw new LogicException('Estas pruebas requieren SQLite en memoria para proteger los datos locales.');
        }
    }

    public function test_delete_removes_only_the_selected_character_and_flashes_success(): void
    {
        $personaje = Personaje::create(['nombre' => 'Luke Skywalker', 'genero' => 'masculino']);
        $otro = Personaje::create(['nombre' => 'Leia Organa', 'genero' => 'femenino']);

        $this->delete(route('personajes.destroy', $personaje))
            ->assertRedirect(route('personajes.index'))
            ->assertSessionHas('inertia.flash_data.exito', 'Luke Skywalker fue eliminado del archivo.');

        $this->assertModelMissing($personaje);
        $this->assertModelExists($otro);
        $this->assertDatabaseCount('personajes', 1);
    }

    public function test_deleting_a_missing_character_returns_not_found(): void
    {
        $this->delete(route('personajes.destroy', 999))->assertNotFound();
    }

    public function test_database_failure_preserves_the_character_and_returns_an_error(): void
    {
        $personaje = Personaje::create(['nombre' => 'Luke Skywalker', 'genero' => 'masculino']);
        Personaje::deleting(function (): void {
            throw new QueryException('sqlite', 'delete from personajes where id = ?', [], new PDOException('Fallo simulado de prueba'));
        });

        $this->from(route('personajes.index'))
            ->delete(route('personajes.destroy', $personaje))
            ->assertRedirect(route('personajes.index'))
            ->assertSessionHasErrors(['eliminar' => 'No pudimos eliminar el personaje. Intentá nuevamente.'])
            ->assertSessionMissing('inertia.flash_data.exito');

        $this->assertModelExists($personaje);
    }
}
