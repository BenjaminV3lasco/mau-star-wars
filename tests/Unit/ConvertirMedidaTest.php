<?php

namespace Tests\Unit;

use App\Services\SwapiService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConvertirMedidaTest extends TestCase
{
    #[DataProvider('medidas')]
    public function test_converts_swapi_measures_to_integers_within_limits(?string $valor, int $maximo, ?int $esperado): void
    {
        $this->assertSame($esperado, SwapiService::convertirMedida($valor, $maximo));
    }

    public static function medidas(): array
    {
        return [
            'entero' => ['172', 300, 172],
            'decimal se trunca' => ['78.2', 500, 78],
            'miles con coma' => ['1,358', 2000, 1358],
            'supera el máximo' => ['1,358', 500, null],
            'cero no es positivo' => ['0', 300, null],
            'desconocido' => ['unknown', 300, null],
            'vacío' => ['', 300, null],
            'nulo' => [null, 300, null],
            'con espacios' => [' 96 ', 300, 96],
            'texto mezclado' => ['80kg', 500, null],
        ];
    }
}
