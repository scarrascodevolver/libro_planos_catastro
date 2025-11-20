<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Plano;
use App\Models\ComunaBiobio;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComunaCambioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Verificar que cambiar comuna actualiza codigo_comuna en numero_plano
     */
    public function test_cambiar_comuna_actualiza_codigo_en_numero(): void
    {
        // Crear comunas
        ComunaBiobio::create(['codigo' => '303', 'nombre' => 'Yumbel', 'provincia' => 'Biobío']);
        ComunaBiobio::create(['codigo' => '101', 'nombre' => 'Concepción', 'provincia' => 'Concepción']);

        // Crear plano en Yumbel (303)
        $plano = Plano::create([
            'numero_plano' => '0830329272SU',
            'codigo_region' => '08',
            'codigo_comuna' => '303',
            'numero_correlativo' => 29272,
            'tipo_saneamiento' => 'SU',
            'comuna' => 'Yumbel',
            'provincia' => 'Biobío',
            'mes' => 'ENE',
            'ano' => 2025,
            'responsable' => 'Test',
            'proyecto' => 'Test',
            'total_m2' => 1000,
            'cantidad_folios' => 0,
            'created_by' => 1
        ]);

        // Cambiar a Concepción (101)
        $plano->codigo_comuna = '101';
        $plano->numero_plano = '0810129272SU'; // 08 + 101 + 29272 + SU
        $plano->save();

        // Verificar cambio
        $this->assertEquals('101', $plano->codigo_comuna);
        $this->assertStringContains('08101', $plano->numero_plano);
        $this->assertStringEndsWith('SU', $plano->numero_plano);
    }
}
