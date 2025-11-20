<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plano;
use App\Models\PlanoFolio;
use App\Models\ComunaBiobio;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReasignarPlanoTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $plano;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario con rol registro
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'role' => 'registro'
        ]);

        // Crear comuna de prueba
        ComunaBiobio::create([
            'codigo' => '303',
            'nombre' => 'Yumbel',
            'provincia' => 'Biobío'
        ]);

        // Crear plano de prueba
        $this->plano = Plano::create([
            'numero_plano' => '0830329272SU',
            'codigo_region' => '08',
            'codigo_comuna' => '303',
            'numero_correlativo' => 29272,
            'tipo_saneamiento' => 'SU',
            'comuna' => 'Yumbel',
            'provincia' => 'Biobío',
            'mes' => 'ENE',
            'ano' => 2025,
            'responsable' => 'Alfonso Norambuena',
            'proyecto' => 'CONVENIO-FINANCIAMIENTO',
            'total_hectareas' => 5.0,
            'total_m2' => 50000,
            'cantidad_folios' => 2,
            'created_by' => $this->user->id
        ]);

        // Crear folios de prueba
        PlanoFolio::create([
            'plano_id' => $this->plano->id,
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

        PlanoFolio::create([
            'plano_id' => $this->plano->id,
            'folio' => '789012',
            'solicitante' => 'MARIA',
            'apellido_paterno' => 'LOPEZ',
            'apellido_materno' => 'RAMIREZ',
            'tipo_inmueble' => 'HIJUELA',
            'numero_inmueble' => 2,
            'hectareas' => 2.5,
            'm2' => 25000,
            'is_from_matrix' => true
        ]);
    }

    /**
     * Test 1: Reasignar crea un nuevo plano con número correlativo siguiente
     */
    public function test_reasignar_crea_plano_con_nuevo_numero(): void
    {
        $planosAntesCount = Plano::count();

        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/reasignar");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'nuevo_plano_id',
                'nuevo_numero'
            ]);

        // Verificar que ahora hay 2 planos
        $this->assertEquals($planosAntesCount + 1, Plano::count());

        // Verificar que número se incrementó
        $nuevoNumero = $response->json('nuevo_numero');
        $this->assertStringStartsWith('08303', $nuevoNumero);
        $this->assertStringEndsWith('SU', $nuevoNumero);

        // El nuevo correlativo debe ser 29273 (uno más que 29272)
        $nuevoPlano = Plano::find($response->json('nuevo_plano_id'));
        $this->assertEquals(29273, $nuevoPlano->numero_correlativo);
    }

    /**
     * Test 2: Reasignar duplica todos los datos del plano original
     */
    public function test_reasignar_duplica_datos_plano(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/reasignar");

        $response->assertStatus(200);

        $nuevoPlano = Plano::find($response->json('nuevo_plano_id'));

        // Verificar que datos generales se copiaron
        $this->assertEquals($this->plano->comuna, $nuevoPlano->comuna);
        $this->assertEquals($this->plano->provincia, $nuevoPlano->provincia);
        $this->assertEquals($this->plano->tipo_saneamiento, $nuevoPlano->tipo_saneamiento);
        $this->assertEquals($this->plano->responsable, $nuevoPlano->responsable);
        $this->assertEquals($this->plano->proyecto, $nuevoPlano->proyecto);
        $this->assertEquals($this->plano->mes, $nuevoPlano->mes);
        $this->assertEquals($this->plano->ano, $nuevoPlano->ano);

        // Verificar que código de región y comuna se mantienen
        $this->assertEquals($this->plano->codigo_region, $nuevoPlano->codigo_region);
        $this->assertEquals($this->plano->codigo_comuna, $nuevoPlano->codigo_comuna);

        // Verificar que número de plano es diferente
        $this->assertNotEquals($this->plano->numero_plano, $nuevoPlano->numero_plano);
    }

    /**
     * Test 3: Reasignar duplica todos los folios del plano original
     */
    public function test_reasignar_duplica_folios(): void
    {
        $foliosOriginalesCount = $this->plano->folios()->count();

        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/reasignar");

        $response->assertStatus(200);

        $nuevoPlano = Plano::find($response->json('nuevo_plano_id'));

        // Verificar cantidad de folios
        $this->assertEquals($foliosOriginalesCount, $nuevoPlano->folios()->count());

        // Verificar que folios se copiaron correctamente
        $foliosOriginales = $this->plano->folios()->orderBy('id')->get();
        $foliosNuevos = $nuevoPlano->folios()->orderBy('id')->get();

        for ($i = 0; $i < $foliosOriginalesCount; $i++) {
            $this->assertEquals($foliosOriginales[$i]->folio, $foliosNuevos[$i]->folio);
            $this->assertEquals($foliosOriginales[$i]->solicitante, $foliosNuevos[$i]->solicitante);
            $this->assertEquals($foliosOriginales[$i]->apellido_paterno, $foliosNuevos[$i]->apellido_paterno);
            $this->assertEquals($foliosOriginales[$i]->tipo_inmueble, $foliosNuevos[$i]->tipo_inmueble);
            $this->assertEquals($foliosOriginales[$i]->hectareas, $foliosNuevos[$i]->hectareas);
            $this->assertEquals($foliosOriginales[$i]->m2, $foliosNuevos[$i]->m2);
        }
    }

    /**
     * Test 4: Reasignar duplica correctamente los totales
     */
    public function test_reasignar_duplica_totales(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/reasignar");

        $response->assertStatus(200);

        $nuevoPlano = Plano::find($response->json('nuevo_plano_id'));

        // Verificar totales
        $this->assertEquals($this->plano->total_hectareas, $nuevoPlano->total_hectareas);
        $this->assertEquals($this->plano->total_m2, $nuevoPlano->total_m2);
        $this->assertEquals($this->plano->cantidad_folios, $nuevoPlano->cantidad_folios);
    }

    /**
     * Test 5: Reasignar múltiples veces incrementa correlativo correctamente
     */
    public function test_reasignar_multiples_veces_incrementa_correlativo(): void
    {
        // Primera reasignación: 29272 → 29273
        $response1 = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/reasignar");

        $response1->assertStatus(200);
        $plano1 = Plano::find($response1->json('nuevo_plano_id'));
        $this->assertEquals(29273, $plano1->numero_correlativo);

        // Segunda reasignación: 29273 → 29274
        $response2 = $this->actingAs($this->user)
            ->postJson("/planos/{$plano1->id}/reasignar");

        $response2->assertStatus(200);
        $plano2 = Plano::find($response2->json('nuevo_plano_id'));
        $this->assertEquals(29274, $plano2->numero_correlativo);

        // Tercera reasignación: 29274 → 29275
        $response3 = $this->actingAs($this->user)
            ->postJson("/planos/{$plano2->id}/reasignar");

        $response3->assertStatus(200);
        $plano3 = Plano::find($response3->json('nuevo_plano_id'));
        $this->assertEquals(29275, $plano3->numero_correlativo);

        // Verificar que ahora hay 4 planos totales
        $this->assertEquals(4, Plano::count());
    }

    /**
     * Test 6: Plano original permanece inalterado después de reasignar
     */
    public function test_plano_original_no_cambia_despues_reasignar(): void
    {
        $numeroOriginal = $this->plano->numero_plano;
        $correlativoOriginal = $this->plano->numero_correlativo;
        $foliosOriginales = $this->plano->folios()->count();

        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/reasignar");

        $response->assertStatus(200);

        // Refrescar plano original
        $this->plano->refresh();

        // Verificar que no cambió
        $this->assertEquals($numeroOriginal, $this->plano->numero_plano);
        $this->assertEquals($correlativoOriginal, $this->plano->numero_correlativo);
        $this->assertEquals($foliosOriginales, $this->plano->folios()->count());
    }

    /**
     * Test 7: Mantiene tipo de saneamiento en nuevo número
     */
    public function test_reasignar_mantiene_tipo_saneamiento(): void
    {
        // Probar con cada tipo de saneamiento
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
                'created_by' => $this->user->id
            ]);

            $response = $this->actingAs($this->user)
                ->postJson("/planos/{$plano->id}/reasignar");

            $response->assertStatus(200);

            $nuevoPlano = Plano::find($response->json('nuevo_plano_id'));

            // Verificar que tipo se mantuvo
            $this->assertEquals($tipo, $nuevoPlano->tipo_saneamiento);
            $this->assertStringEndsWith($tipo, $nuevoPlano->numero_plano);
        }
    }
}
