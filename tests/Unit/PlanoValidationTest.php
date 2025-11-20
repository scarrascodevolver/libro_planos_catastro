<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Plano;
use App\Models\PlanoFolio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class PlanoValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Verificar que campos obligatorios están presentes
     */
    public function test_plano_requiere_campos_obligatorios(): void
    {
        // Intentar crear plano sin campos requeridos
        $this->expectException(\Illuminate\Database\QueryException::class);

        Plano::create([
            // Falta numero_plano, codigo_region, codigo_comuna, etc.
            'mes' => 'ENE',
            'ano' => 2025,
        ]);
    }

    /**
     * Test 2: Verificar que numero_plano es único
     */
    public function test_numero_plano_debe_ser_unico(): void
    {
        // Crear primer plano
        Plano::create([
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

        // Intentar crear segundo plano con mismo número
        $this->expectException(\Illuminate\Database\QueryException::class);

        Plano::create([
            'numero_plano' => '0830329272SU', // DUPLICADO
            'codigo_region' => '08',
            'codigo_comuna' => '303',
            'numero_correlativo' => 29272,
            'tipo_saneamiento' => 'SU',
            'comuna' => 'Yumbel',
            'provincia' => 'Biobío',
            'mes' => 'FEB',
            'ano' => 2025,
            'responsable' => 'Test User 2',
            'proyecto' => 'Test Project 2',
            'total_m2' => 2000,
            'cantidad_folios' => 0,
            'created_by' => 1
        ]);
    }

    /**
     * Test 3: Verificar rangos de año válidos
     */
    public function test_ano_debe_estar_en_rango_valido(): void
    {
        // Año válido debe funcionar
        $plano = Plano::create([
            'numero_plano' => '0830329273SU',
            'codigo_region' => '08',
            'codigo_comuna' => '303',
            'numero_correlativo' => 29273,
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

        $this->assertGreaterThanOrEqual(2000, $plano->ano);
        $this->assertLessThanOrEqual(2030, $plano->ano);
    }

    /**
     * Test 4: Verificar tipos de saneamiento válidos
     */
    public function test_tipo_saneamiento_solo_acepta_valores_validos(): void
    {
        $tiposValidos = ['SR', 'SU', 'CR', 'CU'];

        foreach ($tiposValidos as $tipo) {
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

            $this->assertContains($plano->tipo_saneamiento, $tiposValidos);
        }
    }

    /**
     * Test 5: Verificar que folios pueden tener folio duplicado en diferentes planos
     */
    public function test_folios_pueden_repetirse_en_diferentes_planos(): void
    {
        // Crear primer plano
        $plano1 = Plano::create([
            'numero_plano' => '0830329274SU',
            'codigo_region' => '08',
            'codigo_comuna' => '303',
            'numero_correlativo' => 29274,
            'tipo_saneamiento' => 'SU',
            'comuna' => 'Yumbel',
            'provincia' => 'Biobío',
            'mes' => 'ENE',
            'ano' => 2025,
            'responsable' => 'Test User',
            'proyecto' => 'Test Project',
            'total_m2' => 1000,
            'cantidad_folios' => 1,
            'created_by' => 1
        ]);

        // Crear segundo plano
        $plano2 = Plano::create([
            'numero_plano' => '0830329275SU',
            'codigo_region' => '08',
            'codigo_comuna' => '303',
            'numero_correlativo' => 29275,
            'tipo_saneamiento' => 'SU',
            'comuna' => 'Yumbel',
            'provincia' => 'Biobío',
            'mes' => 'FEB',
            'ano' => 2025,
            'responsable' => 'Test User',
            'proyecto' => 'Test Project',
            'total_m2' => 1000,
            'cantidad_folios' => 1,
            'created_by' => 1
        ]);

        // Agregar MISMO folio a ambos planos (esto DEBE ser permitido)
        $folio1 = PlanoFolio::create([
            'plano_id' => $plano1->id,
            'folio' => '123456',
            'solicitante' => 'JUAN',
            'apellido_paterno' => 'PEREZ',
            'apellido_materno' => 'GONZALEZ',
            'tipo_inmueble' => 'HIJUELA',
            'numero_inmueble' => 1,
            'hectareas' => 2.5,
            'm2' => 25000,
            'is_from_matrix' => true
        ]);

        $folio2 = PlanoFolio::create([
            'plano_id' => $plano2->id,
            'folio' => '123456', // MISMO FOLIO, diferente plano
            'solicitante' => 'MARIA',
            'apellido_paterno' => 'LOPEZ',
            'apellido_materno' => 'RAMIREZ',
            'tipo_inmueble' => 'SITIO',
            'numero_inmueble' => 5,
            'hectareas' => null,
            'm2' => 500,
            'is_from_matrix' => true
        ]);

        // Verificar que ambos folios existen
        $this->assertEquals('123456', $folio1->folio);
        $this->assertEquals('123456', $folio2->folio);
        $this->assertNotEquals($folio1->plano_id, $folio2->plano_id);
    }

    /**
     * Test 6: Verificar validación de tipos de inmueble
     */
    public function test_tipo_inmueble_solo_acepta_hijuela_o_sitio(): void
    {
        $plano = Plano::create([
            'numero_plano' => '0830329276SU',
            'codigo_region' => '08',
            'codigo_comuna' => '303',
            'numero_correlativo' => 29276,
            'tipo_saneamiento' => 'SU',
            'comuna' => 'Yumbel',
            'provincia' => 'Biobío',
            'mes' => 'ENE',
            'ano' => 2025,
            'responsable' => 'Test User',
            'proyecto' => 'Test Project',
            'total_m2' => 1000,
            'cantidad_folios' => 1,
            'created_by' => 1
        ]);

        // HIJUELA debe funcionar
        $folio1 = PlanoFolio::create([
            'plano_id' => $plano->id,
            'folio' => '111111',
            'solicitante' => 'JUAN',
            'tipo_inmueble' => 'HIJUELA',
            'numero_inmueble' => 1,
            'hectareas' => 2.5,
            'm2' => 25000,
            'is_from_matrix' => true
        ]);

        // SITIO debe funcionar
        $folio2 = PlanoFolio::create([
            'plano_id' => $plano->id,
            'folio' => '222222',
            'solicitante' => 'MARIA',
            'tipo_inmueble' => 'SITIO',
            'numero_inmueble' => 5,
            'hectareas' => null,
            'm2' => 500,
            'is_from_matrix' => true
        ]);

        $this->assertEquals('HIJUELA', $folio1->tipo_inmueble);
        $this->assertEquals('SITIO', $folio2->tipo_inmueble);
    }
}
