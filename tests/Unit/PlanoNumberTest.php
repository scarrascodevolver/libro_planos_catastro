<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Plano;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanoNumberTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Verificar formato correcto del número de plano
     * Formato: 08 + codigo_comuna (3 dígitos) + correlativo + tipo (2 letras)
     */
    public function test_numero_plano_tiene_formato_correcto(): void
    {
        // Crear un plano de prueba
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
            'responsable' => 'Test User',
            'proyecto' => 'Test Project',
            'total_m2' => 1000,
            'cantidad_folios' => 0,
            'created_by' => 1
        ]);

        // Verificar que el número tiene el formato correcto
        $this->assertEquals('0830329272SU', $plano->numero_plano);

        // Verificar longitud (2 + 3 + variable + 2)
        $this->assertGreaterThanOrEqual(9, strlen($plano->numero_plano));

        // Verificar que comienza con código región
        $this->assertStringStartsWith('08', $plano->numero_plano);

        // Verificar que termina con tipo saneamiento
        $this->assertStringEndsWith('SU', $plano->numero_plano);
    }

    /**
     * Test 2: Verificar que diferentes tipos de saneamiento funcionan
     */
    public function test_numero_plano_acepta_todos_tipos_saneamiento(): void
    {
        $tipos = ['SR', 'SU', 'CR', 'CU'];

        foreach ($tipos as $tipo) {
            $plano = Plano::create([
                'numero_plano' => '08303' . rand(10000, 99999) . $tipo,
                'codigo_region' => '08',
                'codigo_comuna' => '303',
                'numero_correlativo' => rand(10000, 99999),
                'tipo_saneamiento' => $tipo,
                'comuna' => 'Yumbel',
                'provincia' => 'Biobío',
                'mes' => 'ENE',
                'ano' => 2025,
                'responsable' => 'Test User',
                'proyecto' => 'Test Project',
                'total_m2' => 1000,
                'cantidad_folios' => 0,
                'created_by' => 1
            ]);

            // Verificar que termina con el tipo correcto
            $this->assertStringEndsWith($tipo, $plano->numero_plano);
        }
    }

    /**
     * Test 3: Verificar código de comuna en número de plano
     */
    public function test_codigo_comuna_esta_en_posicion_correcta(): void
    {
        $plano = Plano::create([
            'numero_plano' => '0820529800SR',
            'codigo_region' => '08',
            'codigo_comuna' => '205', // Curanilahue
            'numero_correlativo' => 29800,
            'tipo_saneamiento' => 'SR',
            'comuna' => 'Curanilahue',
            'provincia' => 'Arauco',
            'mes' => 'FEB',
            'ano' => 2025,
            'responsable' => 'Test User',
            'proyecto' => 'Test Project',
            'total_m2' => 1500,
            'cantidad_folios' => 0,
            'created_by' => 1
        ]);

        // Extraer código de comuna del número (posiciones 2-5)
        $codigoExtraido = substr($plano->numero_plano, 2, 3);

        // Verificar que coincide con codigo_comuna
        $this->assertEquals('205', $codigoExtraido);
        $this->assertEquals($plano->codigo_comuna, $codigoExtraido);
    }
}
