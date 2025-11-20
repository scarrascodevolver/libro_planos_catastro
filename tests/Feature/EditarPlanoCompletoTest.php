<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plano;
use App\Models\PlanoFolio;
use App\Models\ComunaBiobio;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditarPlanoCompletoTest extends TestCase
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

        ComunaBiobio::create([
            'codigo' => '101',
            'nombre' => 'Concepción',
            'provincia' => 'Concepción'
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
     * Test 1: Verificar que endpoint GET /planos/{id}/edit devuelve datos correctos
     */
    public function test_endpoint_edit_devuelve_datos_plano(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson("/planos/{$this->plano->id}/edit");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'plano' => [
                    'id',
                    'numero_plano',
                    'comuna',
                    'provincia',
                    'responsable',
                    'proyecto',
                    'mes',
                    'ano',
                    'tipo_saneamiento',
                    'folios'
                ]
            ]);

        $data = $response->json();
        $this->assertEquals('0830329272SU', $data['plano']['numero_plano']);
        $this->assertCount(2, $data['plano']['folios']);
    }

    /**
     * Test 2: Editar plano completo - cambiar datos generales y folios
     */
    public function test_editar_plano_completo_actualiza_correctamente(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/update-completo", [
                'comuna' => 'Yumbel',
                'provincia' => 'Biobío',
                'tipo_saneamiento' => 'SR', // Cambio de SU a SR
                'responsable' => 'Carlos Pérez', // Cambio
                'proyecto' => 'FISCAL', // Cambio
                'mes' => 'FEB', // Cambio
                'ano' => 2025,
                'observaciones' => 'Plano editado en test',
                'folios' => [
                    [
                        'id' => $this->plano->folios[0]->id,
                        'folio' => '123456',
                        'solicitante' => 'PEDRO', // Cambio
                        'apellido_paterno' => 'SILVA',
                        'apellido_materno' => 'MORALES',
                        'tipo_inmueble' => 'HIJUELA',
                        'numero_inmueble' => 1,
                        'hectareas' => 3.0, // Cambio
                        'm2' => 30000 // Cambio
                    ],
                    [
                        'id' => $this->plano->folios[1]->id,
                        'folio' => '789012',
                        'solicitante' => 'MARIA',
                        'apellido_paterno' => 'LOPEZ',
                        'apellido_materno' => 'RAMIREZ',
                        'tipo_inmueble' => 'SITIO', // Cambio
                        'numero_inmueble' => 5,
                        'hectareas' => null, // NULL para sitios
                        'm2' => 1000
                    ]
                ]
            ]);

        $response->assertStatus(200);

        // Verificar que plano se actualizó
        $this->plano->refresh();
        $this->assertEquals('SR', $this->plano->tipo_saneamiento);
        $this->assertEquals('Carlos Pérez', $this->plano->responsable);
        $this->assertEquals('FISCAL', $this->plano->proyecto);
        $this->assertEquals('FEB', $this->plano->mes);

        // Verificar que folios se actualizaron
        $folios = $this->plano->folios()->get();
        $this->assertEquals('PEDRO', $folios[0]->solicitante);
        $this->assertEquals(3.0, (float) $folios[0]->hectareas);
        $this->assertEquals(30000, $folios[0]->m2);
        $this->assertEquals('SITIO', $folios[1]->tipo_inmueble);
    }

    /**
     * Test 3: Editar plano - agregar nuevo folio
     */
    public function test_editar_plano_agregar_nuevo_folio(): void
    {
        $foliosOriginales = $this->plano->folios()->count();

        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/update-completo", [
                'comuna' => 'Yumbel',
                'provincia' => 'Biobío',
                'tipo_saneamiento' => 'SU',
                'responsable' => 'Alfonso Norambuena',
                'proyecto' => 'CONVENIO-FINANCIAMIENTO',
                'mes' => 'ENE',
                'ano' => 2025,
                'observaciones' => '',
                'folios' => [
                    // Folios existentes
                    [
                        'id' => $this->plano->folios[0]->id,
                        'folio' => '123456',
                        'solicitante' => 'JUAN',
                        'apellido_paterno' => 'PEREZ',
                        'apellido_materno' => 'GONZALEZ',
                        'tipo_inmueble' => 'HIJUELA',
                        'numero_inmueble' => 1,
                        'hectareas' => 2.5,
                        'm2' => 25000
                    ],
                    [
                        'id' => $this->plano->folios[1]->id,
                        'folio' => '789012',
                        'solicitante' => 'MARIA',
                        'apellido_paterno' => 'LOPEZ',
                        'apellido_materno' => 'RAMIREZ',
                        'tipo_inmueble' => 'HIJUELA',
                        'numero_inmueble' => 2,
                        'hectareas' => 2.5,
                        'm2' => 25000
                    ],
                    // NUEVO FOLIO
                    [
                        'folio' => '999888',
                        'solicitante' => 'CARLOS',
                        'apellido_paterno' => 'ROJAS',
                        'apellido_materno' => 'CASTRO',
                        'tipo_inmueble' => 'SITIO',
                        'numero_inmueble' => 3,
                        'hectareas' => null,
                        'm2' => 750
                    ]
                ]
            ]);

        $response->assertStatus(200);

        // Verificar que se agregó el folio
        $this->plano->refresh();
        $this->assertEquals($foliosOriginales + 1, $this->plano->folios()->count());

        $nuevoFolio = $this->plano->folios()->where('folio', '999888')->first();
        $this->assertNotNull($nuevoFolio);
        $this->assertEquals('CARLOS', $nuevoFolio->solicitante);
        $this->assertEquals('SITIO', $nuevoFolio->tipo_inmueble);
    }

    /**
     * Test 4: Editar plano - eliminar folio existente
     */
    public function test_editar_plano_eliminar_folio(): void
    {
        $foliosOriginales = $this->plano->folios()->count();

        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/update-completo", [
                'comuna' => 'Yumbel',
                'provincia' => 'Biobío',
                'tipo_saneamiento' => 'SU',
                'responsable' => 'Alfonso Norambuena',
                'proyecto' => 'CONVENIO-FINANCIAMIENTO',
                'mes' => 'ENE',
                'ano' => 2025,
                'observaciones' => '',
                'folios' => [
                    // Solo enviar el primer folio (eliminar el segundo)
                    [
                        'id' => $this->plano->folios[0]->id,
                        'folio' => '123456',
                        'solicitante' => 'JUAN',
                        'apellido_paterno' => 'PEREZ',
                        'apellido_materno' => 'GONZALEZ',
                        'tipo_inmueble' => 'HIJUELA',
                        'numero_inmueble' => 1,
                        'hectareas' => 2.5,
                        'm2' => 25000
                    ]
                ]
            ]);

        $response->assertStatus(200);

        // Verificar que se eliminó el folio
        $this->plano->refresh();
        $this->assertEquals($foliosOriginales - 1, $this->plano->folios()->count());
        $this->assertEquals(1, $this->plano->folios()->count());
    }

    /**
     * Test 5: Cambiar comuna actualiza código de comuna y número de plano
     */
    public function test_cambiar_comuna_actualiza_numero_plano(): void
    {
        $numeroOriginal = $this->plano->numero_plano; // 0830329272SU

        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/update-completo", [
                'comuna' => 'Concepción', // Cambio de Yumbel (303) a Concepción (101)
                'provincia' => 'Concepción',
                'tipo_saneamiento' => 'SU',
                'responsable' => 'Alfonso Norambuena',
                'proyecto' => 'CONVENIO-FINANCIAMIENTO',
                'mes' => 'ENE',
                'ano' => 2025,
                'observaciones' => '',
                'folios' => [
                    [
                        'id' => $this->plano->folios[0]->id,
                        'folio' => '123456',
                        'solicitante' => 'JUAN',
                        'apellido_paterno' => 'PEREZ',
                        'apellido_materno' => 'GONZALEZ',
                        'tipo_inmueble' => 'HIJUELA',
                        'numero_inmueble' => 1,
                        'hectareas' => 2.5,
                        'm2' => 25000
                    ]
                ]
            ]);

        $response->assertStatus(200);

        // Verificar que número cambió de 08303... a 08101...
        $this->plano->refresh();
        $this->assertEquals('101', $this->plano->codigo_comuna); // Cambió
        $this->assertEquals('Concepción', $this->plano->comuna);

        // Verificar formato: 08 + 101 + 29272 + SU
        $this->assertStringStartsWith('08101', $this->plano->numero_plano);
        $this->assertStringEndsWith('SU', $this->plano->numero_plano);
        $this->assertNotEquals($numeroOriginal, $this->plano->numero_plano);
    }

    /**
     * Test 6: Verificar recálculo automático de totales
     */
    public function test_recalculo_automatico_de_totales(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson("/planos/{$this->plano->id}/update-completo", [
                'comuna' => 'Yumbel',
                'provincia' => 'Biobío',
                'tipo_saneamiento' => 'SU',
                'responsable' => 'Alfonso Norambuena',
                'proyecto' => 'CONVENIO-FINANCIAMIENTO',
                'mes' => 'ENE',
                'ano' => 2025,
                'observaciones' => '',
                'folios' => [
                    [
                        'id' => $this->plano->folios[0]->id,
                        'folio' => '123456',
                        'solicitante' => 'JUAN',
                        'apellido_paterno' => 'PEREZ',
                        'apellido_materno' => 'GONZALEZ',
                        'tipo_inmueble' => 'HIJUELA',
                        'numero_inmueble' => 1,
                        'hectareas' => 10.0, // Cambio significativo
                        'm2' => 100000 // Cambio significativo
                    ],
                    [
                        'id' => $this->plano->folios[1]->id,
                        'folio' => '789012',
                        'solicitante' => 'MARIA',
                        'apellido_paterno' => 'LOPEZ',
                        'apellido_materno' => 'RAMIREZ',
                        'tipo_inmueble' => 'HIJUELA',
                        'numero_inmueble' => 2,
                        'hectareas' => 5.0,
                        'm2' => 50000
                    ]
                ]
            ]);

        $response->assertStatus(200);

        // Verificar recálculo
        $this->plano->refresh();
        $this->assertEquals(15.0, (float) $this->plano->total_hectareas); // 10 + 5
        $this->assertEquals(150000, $this->plano->total_m2); // 100000 + 50000
        $this->assertEquals(2, $this->plano->cantidad_folios);
    }
}
