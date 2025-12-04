<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plano;
use App\Models\PlanoFolio;
use App\Models\MatrixImport;
use App\Models\ComunaBiobio;
use App\Models\SessionControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlanoCreacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isRegistro()) {
                abort(403, 'Solo usuarios con rol "registro" pueden crear planos');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $comunas = ComunaBiobio::getParaSelect();

        return view('admin.planos.crear', compact('comunas'));
    }

    public function getUltimoCorrelativo()
    {
        // Buscar el último plano por número correlativo
        $ultimoPlano = Plano::orderBy('numero_correlativo', 'desc')->first();

        // Si hay planos, devolver el número completo del último
        if ($ultimoPlano !== null) {
            // Generar número completo: 08 + codigo_comuna + correlativo(5 dígitos) + tipo_saneamiento
            $numeroCompleto = $ultimoPlano->codigo_region
                            . $ultimoPlano->codigo_comuna
                            . str_pad($ultimoPlano->numero_correlativo, 5, '0', STR_PAD_LEFT)
                            . $ultimoPlano->tipo_saneamiento;

            return response()->json([
                'ultimo' => $numeroCompleto,
                'ultimoCorrelativo' => $ultimoPlano->numero_correlativo,
                'proximo' => $ultimoPlano->numero_correlativo + 1,
                'hayDatos' => true
            ]);
        }

        // Si no hay planos, indicar que debe importar históricos primero
        return response()->json([
            'ultimo' => null,
            'ultimoCorrelativo' => null,
            'proximo' => null,
            'hayDatos' => false,
            'mensaje' => 'Debe importar planos históricos antes de crear nuevos planos.'
        ]);
    }

    public function buscarFolio(Request $request)
    {
        $request->validate([
            'folio' => 'required|string'
        ]);

        $matrix = MatrixImport::buscarPorFolio($request->folio);

        if (!$matrix) {
            return response()->json([
                'encontrado' => false,
                'message' => 'Folio no encontrado en Matrix'
            ]);
        }

        // Verificar si ya está usado y en qué plano
        $folioUsado = PlanoFolio::with('plano')->where('folio', $request->folio)->first();

        if ($folioUsado) {
            // Folio YA USADO - No devolver datos, solo información del plano donde está
            $plano = $folioUsado->plano;

            // Generar número completo: 08 + codigo_comuna + numero_correlativo(5 dígitos) + tipo_saneamiento
            $numeroCompleto = $plano->codigo_region
                            . $plano->codigo_comuna
                            . str_pad($plano->numero_correlativo, 5, '0', STR_PAD_LEFT)
                            . $plano->tipo_saneamiento;

            // Construir nombre completo del solicitante
            $nombreCompleto = trim($folioUsado->solicitante
                                . ' ' . $folioUsado->apellido_paterno
                                . ' ' . $folioUsado->apellido_materno);

            return response()->json([
                'encontrado' => true,
                'yaUsado' => true,
                'datos' => null,
                'planoExistente' => [
                    'numero' => $numeroCompleto,
                    'solicitante' => $nombreCompleto,
                    'comuna' => $plano->comuna,
                    'responsable' => $plano->responsable,
                    'tipo' => $plano->tipo_saneamiento
                ],
                'message' => 'Este folio ya está usado en el plano: ' . $numeroCompleto . ' (Solicitante: ' . $nombreCompleto . ')'
            ]);
        }

        // Folio NO USADO - Devolver datos para auto-completar
        $comunaBiobio = ComunaBiobio::where('nombre', $matrix->comuna)->first();
        $codigoComuna = $comunaBiobio ? $comunaBiobio->getCodigoParaPlano() : '000';

        return response()->json([
            'encontrado' => true,
            'yaUsado' => false,
            'datos' => [
                'folio' => $matrix->folio,
                'solicitante' => $matrix->nombres,
                'apellido_paterno' => $matrix->apellido_paterno,
                'apellido_materno' => $matrix->apellido_materno,
                'comuna' => $matrix->comuna,
                'codigo_comuna' => $codigoComuna,
                'responsable' => $matrix->responsable,
                'proyecto' => $matrix->convenio_financiamiento,
                'tipo_inmueble' => $matrix->tipo_inmueble,
                'is_from_matrix' => true
            ],
            'message' => null
        ]);
    }

    public function buscarFoliosMasivos(Request $request)
    {
        $request->validate([
            'folios' => 'required|string'
        ]);

        $foliosTexto = $request->folios;
        $foliosArray = array_map('trim', preg_split('/[\r\n,;]+/', $foliosTexto));
        $foliosArray = array_filter($foliosArray); // Remover vacíos

        $encontrados = [];
        $noEncontrados = [];
        $yaUsados = [];

        foreach ($foliosArray as $folio) {
            $matrix = MatrixImport::buscarPorFolio($folio);

            if (!$matrix) {
                $noEncontrados[] = $folio;
                continue;
            }

            $yaUsado = PlanoFolio::where('folio', $folio)->exists();
            if ($yaUsado) {
                $yaUsados[] = $folio;
            }

            $encontrados[] = [
                'folio' => $matrix->folio,
                'solicitante' => $matrix->nombres,
                'apellido_paterno' => $matrix->apellido_paterno,
                'apellido_materno' => $matrix->apellido_materno,
                'comuna' => $matrix->comuna,
                'responsable' => $matrix->responsable,
                'proyecto' => $matrix->convenio_financiamiento,
                'tipo_inmueble' => $matrix->tipo_inmueble,
                'is_from_matrix' => true,
                'yaUsado' => $yaUsado
            ];
        }

        return response()->json([
            'totalProcesados' => count($foliosArray),
            'encontrados' => $encontrados,
            'noEncontrados' => $noEncontrados,
            'yaUsados' => $yaUsados,
            'resumen' => [
                'encontrados' => count($encontrados),
                'noEncontrados' => count($noEncontrados),
                'yaUsados' => count($yaUsados)
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipo_plano' => 'required|string|in:matrix,manual',
            'tipo_ubicacion' => 'required|string|in:SU,SR,CU,CR',
            'codigo_comuna' => 'required|string|size:3',
            'comuna_nombre' => 'required|string',
            'responsable' => 'required|string|max:255',
            'proyecto' => 'required|string|max:255',
            'observaciones' => 'nullable|string',
            'archivo' => 'nullable|string|max:255',
            'tubo' => 'nullable|string|max:255',
            'tela' => 'nullable|string|max:255',
            'archivo_digital' => 'nullable|string|max:255',
            'folios' => 'required|array|min:1|max:150',
            'folios.*.folio' => 'nullable|string|max:50',
            'folios.*.solicitante' => 'required|string|max:255',
            'folios.*.apellido_paterno' => 'nullable|string|max:255',
            'folios.*.apellido_materno' => 'nullable|string|max:255',
            'folios.*.tipo_inmueble' => 'required|in:HIJUELA,SITIO',
            'folios.*.numero_inmueble' => 'nullable|integer',
            'folios.*.hectareas' => 'nullable|numeric|min:0',
            'folios.*.m2' => 'nullable|numeric|min:0',
            'folios.*.is_from_matrix' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validación adicional: cada folio debe tener al menos hectáreas o m²
        foreach ($request->folios as $index => $folio) {
            $tieneHectareas = !empty($folio['hectareas']) && $folio['hectareas'] > 0;
            $tieneM2 = !empty($folio['m2']) && $folio['m2'] > 0;

            if (!$tieneHectareas && !$tieneM2) {
                return response()->json([
                    'success' => false,
                    'message' => "Folio " . ($folio['folio'] ?? '#' . ($index + 1)) . ": Debe ingresar al menos Hectáreas o M²"
                ], 422);
            }
        }

        // Validar que el usuario tenga control de sesión activo
        $control = SessionControl::where('user_id', Auth::id())
            ->where('has_control', true)
            ->where('is_active', true)
            ->first();

        if (!$control) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes control de numeración activo. Debes solicitar control antes de crear planos.'
            ], 403);
        }

        // Generar número de plano
        // Buscar el último correlativo directamente del campo numero_correlativo
        $ultimoPlano = Plano::max('numero_correlativo');

        // Validar que existan planos históricos
        if ($ultimoPlano === null) {
            return response()->json([
                'success' => false,
                'message' => 'Debe importar planos históricos antes de crear nuevos planos.'
            ], 403);
        }

        $correlativo = $ultimoPlano + 1;

        // Obtener datos adicionales
        $codigoRegion = '08'; // Región del Biobío
        $tipoSaneamiento = $request->tipo_ubicacion; // SR, SU, CR, CU
        $meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
        $mesActual = $meses[date('n') - 1];
        $anoActual = date('Y');

        // Obtener provincia desde el código de comuna
        $comunaBiobio = ComunaBiobio::where('codigo', $request->codigo_comuna)->first();
        $provincia = $comunaBiobio ? $comunaBiobio->provincia : 'Desconocida';

        // Verificar folios únicos (solo los que no son null o vacíos)
        $foliosNoVacios = collect($request->folios)
            ->pluck('folio')
            ->filter(function($folio) {
                return !empty($folio);
            });

        if ($foliosNoVacios->count() > 0) {
            $foliosExistentes = PlanoFolio::whereIn('folio', $foliosNoVacios)->pluck('folio');
            if ($foliosExistentes->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los siguientes folios ya están en uso: ' . $foliosExistentes->join(', ')
                ]);
            }
        }

        DB::beginTransaction();
        try {
            // Calcular totales
            $totalHectareas = collect($request->folios)->sum('hectareas');
            $totalM2 = collect($request->folios)->sum('m2');
            $cantidadFolios = count($request->folios);

            // Crear plano con todos los campos requeridos
            $plano = Plano::create([
                'numero_plano' => (string) $correlativo,  // Solo el correlativo
                'codigo_region' => $codigoRegion,
                'codigo_comuna' => $request->codigo_comuna,
                'numero_correlativo' => $correlativo,
                'tipo_saneamiento' => $tipoSaneamiento,
                'provincia' => $provincia,
                'comuna' => $request->comuna_nombre,
                'mes' => $mesActual,
                'ano' => $anoActual,
                'responsable' => $request->responsable,
                'proyecto' => $request->proyecto,
                'total_hectareas' => $totalHectareas > 0 ? $totalHectareas : null,
                'total_m2' => $totalM2,
                'cantidad_folios' => $cantidadFolios,
                'observaciones' => $request->observaciones,
                'archivo' => $request->archivo,
                'tubo' => $request->tubo,
                'tela' => $request->tela,
                'archivo_digital' => $request->archivo_digital,
                'created_by' => Auth::id()
            ]);

            // Crear folios con sus inmuebles
            foreach ($request->folios as $folioData) {
                // Convertir 0 a null para campos vacíos
                $hectareas = (!empty($folioData['hectareas']) && $folioData['hectareas'] > 0) ? $folioData['hectareas'] : null;
                $m2 = (!empty($folioData['m2']) && $folioData['m2'] > 0) ? $folioData['m2'] : null;

                $planoFolio = PlanoFolio::create([
                    'plano_id' => $plano->id,
                    'folio' => $folioData['folio'],
                    'solicitante' => $folioData['solicitante'],
                    'apellido_paterno' => $folioData['apellido_paterno'],
                    'apellido_materno' => $folioData['apellido_materno'],
                    'tipo_inmueble' => $folioData['tipo_inmueble'],
                    'numero_inmueble' => $folioData['numero_inmueble'],
                    'hectareas' => $hectareas,
                    'm2' => $m2,
                    'is_from_matrix' => $folioData['is_from_matrix'],
                    'matrix_folio' => $folioData['is_from_matrix'] ? $folioData['folio'] : null
                ]);

                // Crear inmuebles (desglose de hijuelas/sitios) si existen
                if (!empty($folioData['inmuebles'])) {
                    foreach ($folioData['inmuebles'] as $inmuebleData) {
                        \App\Models\PlanoFolioInmueble::create([
                            'plano_folio_id' => $planoFolio->id,
                            'numero_inmueble' => $inmuebleData['numero_inmueble'],
                            'tipo_inmueble' => $inmuebleData['tipo_inmueble'],
                            'hectareas' => $inmuebleData['hectareas'] ?? null,
                            'm2' => $inmuebleData['m2']
                        ]);
                    }
                }
            }

            DB::commit();

            // Generar número completo para mostrar
            $numeroCompleto = $codigoRegion . $request->codigo_comuna . str_pad($correlativo, 5, '0', STR_PAD_LEFT) . $tipoSaneamiento;

            return response()->json([
                'success' => true,
                'message' => 'Plano creado correctamente',
                'plano' => [
                    'id' => $plano->id,
                    'numero' => $numeroCompleto,
                    'correlativo' => $correlativo,
                    'folios' => $cantidadFolios
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el plano: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validarFolios(Request $request)
    {
        $request->validate([
            'folios' => 'required|array'
        ]);

        $folios = $request->folios;
        $duplicados = [];
        $yaUsados = [];
        $sinMatrix = [];

        // Verificar duplicados en la solicitud
        $foliosUnicos = [];
        foreach ($folios as $folio) {
            if (in_array($folio, $foliosUnicos)) {
                $duplicados[] = $folio;
            } else {
                $foliosUnicos[] = $folio;
            }
        }

        // Verificar ya usados en BD
        $usados = PlanoFolio::whereIn('folio', $foliosUnicos)->pluck('folio')->toArray();
        $yaUsados = array_intersect($foliosUnicos, $usados);

        // Verificar existencia en Matrix
        $enMatrix = MatrixImport::whereIn('folio', $foliosUnicos)->pluck('folio')->toArray();
        $sinMatrix = array_diff($foliosUnicos, $enMatrix);

        return response()->json([
            'valido' => empty($duplicados) && empty($yaUsados),
            'duplicados' => $duplicados,
            'yaUsados' => $yaUsados,
            'sinMatrix' => $sinMatrix,
            'totalValidados' => count($foliosUnicos)
        ]);
    }
}