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

        // Verificar si ya está usado
        $yaUsado = PlanoFolio::where('folio', $request->folio)->exists();

        return response()->json([
            'encontrado' => true,
            'yaUsado' => $yaUsado,
            'datos' => [
                'folio' => $matrix->folio,
                'solicitante' => $matrix->nombres,
                'apellido_paterno' => $matrix->apellido_paterno,
                'apellido_materno' => $matrix->apellido_materno,
                'comuna' => $matrix->comuna,
                'responsable' => $matrix->responsable,
                'proyecto' => $matrix->convenio_financiamiento,
                'tipo_inmueble' => $matrix->tipo_inmueble
            ],
            'message' => $yaUsado ? 'ATENCIÓN: Este folio ya fue usado en otro plano' : null
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
            'folios.*.folio' => 'required|string|max:50',
            'folios.*.solicitante' => 'required|string|max:255',
            'folios.*.apellido_paterno' => 'nullable|string|max:255',
            'folios.*.apellido_materno' => 'nullable|string|max:255',
            'folios.*.tipo_inmueble' => 'required|in:HIJUELA,SITIO',
            'folios.*.numero_inmueble' => 'nullable|integer',
            'folios.*.hectareas' => 'nullable|numeric|min:0',
            'folios.*.m2' => 'required|integer|min:1',
            'folios.*.is_from_matrix' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar control de sesión
        $control = SessionControl::where('user_id', Auth::id())
            ->where('has_control', true)
            ->where('is_active', true)
            ->first();

        if (!$control) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes control de numeración activo'
            ], 403);
        }

        // Generar número de plano
        $ultimoPlano = Plano::selectRaw('MAX(CAST(SUBSTRING(numero_plano, 4, 6) AS UNSIGNED)) as ultimo_correlativo')
            ->where('numero_plano', 'REGEXP', '^08[0-9]{8}[A-Z]{2}$')
            ->first();

        $correlativo = ($ultimoPlano->ultimo_correlativo ?? 329271) + 1;
        $numeroPlano = '08' . $request->codigo_comuna . $correlativo . $request->tipo_ubicacion;

        // Verificar folios únicos
        $foliosExistentes = PlanoFolio::whereIn('folio', collect($request->folios)->pluck('folio'))->pluck('folio');
        if ($foliosExistentes->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Los siguientes folios ya están en uso: ' . $foliosExistentes->join(', ')
            ]);
        }

        DB::beginTransaction();
        try {
            // Calcular totales
            $totalHectareas = collect($request->folios)->sum('hectareas');
            $totalM2 = collect($request->folios)->sum('m2');
            $cantidadFolios = count($request->folios);

            // Crear plano
            $plano = Plano::create([
                'numero_plano' => $numeroPlano,
                'comuna' => $request->comuna_nombre,
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

            // Crear folios
            foreach ($request->folios as $folioData) {
                PlanoFolio::create([
                    'plano_id' => $plano->id,
                    'folio' => $folioData['folio'],
                    'solicitante' => $folioData['solicitante'],
                    'apellido_paterno' => $folioData['apellido_paterno'],
                    'apellido_materno' => $folioData['apellido_materno'],
                    'tipo_inmueble' => $folioData['tipo_inmueble'],
                    'numero_inmueble' => $folioData['numero_inmueble'],
                    'hectareas' => $folioData['hectareas'],
                    'm2' => $folioData['m2'],
                    'is_from_matrix' => $folioData['is_from_matrix'],
                    'matrix_folio' => $folioData['is_from_matrix'] ? $folioData['folio'] : null
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plano creado correctamente',
                'plano' => [
                    'id' => $plano->id,
                    'numero' => $numeroPlano,
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