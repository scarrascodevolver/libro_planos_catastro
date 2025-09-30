<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plano;
use App\Models\PlanoFolio;
use App\Models\ComunaBiobio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DataTables;

class PlanoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        $comunas = ComunaBiobio::orderBy('nombre')->pluck('nombre', 'codigo');
        $responsables = Plano::select('responsable')->distinct()->whereNotNull('responsable')->orderBy('responsable')->pluck('responsable');
        $proyectos = Plano::select('proyecto')->distinct()->whereNotNull('proyecto')->orderBy('proyecto')->pluck('proyecto');
        $anos = Plano::select('ano')->distinct()->whereNotNull('ano')->where('ano', '>', 0)->orderBy('ano', 'desc')->pluck('ano')->unique();

        return view('admin.planos.index', compact('comunas', 'responsables', 'proyectos', 'anos'));
    }

    private function getDataTableData(Request $request)
    {
        $query = Plano::with(['folios' => function($query) {
            $query->orderBy('id');
        }]);

        // Aplicar filtros
        $this->applyFilters($query, $request);

        return DataTables::of($query)
            ->addColumn('acciones', function ($plano) {
                $acciones = '';
                if (Auth::user()->isRegistro()) {
                    $acciones .= '<div class="btn-group">';
                    $acciones .= '<button class="btn btn-sm btn-primary editar-plano" data-id="'.$plano->id.'" title="Editar"><i class="fas fa-edit"></i></button>';
                    $acciones .= '<button class="btn btn-sm btn-warning reasignar-plano" data-id="'.$plano->id.'" title="Reasignar N°"><i class="fas fa-exchange-alt"></i></button>';
                    $acciones .= '</div>';
                }
                return $acciones;
            })
            ->addColumn('folios_display', function ($plano) {
                return $this->getDisplayFolios($plano);
            })
            ->addColumn('solicitante_display', function ($plano) {
                return $this->getSolicitanteDisplay($plano);
            })
            ->addColumn('apellido_paterno_display', function ($plano) {
                return $this->getApellidoPaternoDisplay($plano);
            })
            ->addColumn('apellido_materno_display', function ($plano) {
                return $this->getApellidoMaternoDisplay($plano);
            })
            ->addColumn('hectareas_display', function ($plano) {
                return $plano->total_hectareas ? number_format($plano->total_hectareas, 2) : '-';
            })
            ->addColumn('m2_display', function ($plano) {
                return number_format($plano->total_m2 ?: 0);
            })
            ->addColumn('mes_display', function ($plano) {
                return $plano->mes ?: 'DESCONOCIDO';
            })
            ->addColumn('ano_display', function ($plano) {
                return $plano->ano ?: date('Y');
            })
            ->addColumn('numero_plano_completo', function ($plano) {
                return $this->formatNumeroPlanoCompleto($plano);
            })
            ->addColumn('observaciones_display', function ($plano) {
                return $plano->observaciones ?: '-';
            })
            ->addColumn('archivo_display', function ($plano) {
                return $plano->archivo ?: '-';
            })
            ->addColumn('tubo_display', function ($plano) {
                return $plano->tubo ?: '-';
            })
            ->addColumn('tela_display', function ($plano) {
                return $plano->tela ?: '-';
            })
            ->addColumn('archivo_digital_display', function ($plano) {
                return $plano->archivo_digital ?: '-';
            })
            ->addColumn('created_at_display', function ($plano) {
                return $plano->created_at ? $plano->created_at->format('d/m/Y') : '-';
            })
            ->addColumn('detalles', function ($plano) {
                return '<button class="btn btn-sm btn-primary ver-detalles" data-id="'.$plano->id.'" title="Ver todos los detalles">
                    <i class="fas fa-eye"></i>
                </button>';
            })
            // Configurar búsqueda global personalizada
            ->filter(function ($query) use ($request) {
                if ($request->has('search') && !empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $query->where(function($q) use ($searchValue) {
                        // Búsqueda en campos principales
                        $q->where('numero_plano', 'LIKE', "%{$searchValue}%")
                          ->orWhere('numero_correlativo', 'LIKE', "%{$searchValue}%")
                          ->orWhere('comuna', 'LIKE', "%{$searchValue}%")
                          ->orWhere('responsable', 'LIKE', "%{$searchValue}%")
                          ->orWhere('proyecto', 'LIKE', "%{$searchValue}%")
                          ->orWhere('mes', 'LIKE', "%{$searchValue}%")
                          ->orWhere('ano', 'LIKE', "%{$searchValue}%")
                          ->orWhere('observaciones', 'LIKE', "%{$searchValue}%")
                          ->orWhere('archivo', 'LIKE', "%{$searchValue}%")
                          ->orWhere('tubo', 'LIKE', "%{$searchValue}%")
                          ->orWhere('tela', 'LIKE', "%{$searchValue}%")
                          ->orWhere('archivo_digital', 'LIKE', "%{$searchValue}%")
                          // Búsqueda en número completo concatenado
                          ->orWhereRaw("CONCAT(
                              LPAD(COALESCE(codigo_region, '08'), 2, '0'),
                              LPAD(COALESCE(codigo_comuna, ''), 3, '0'),
                              COALESCE(numero_correlativo, numero_plano),
                              COALESCE(tipo_saneamiento, '')
                          ) LIKE ?", ["%{$searchValue}%"])
                          // Búsqueda en folios relacionados
                          ->orWhereHas('folios', function($folioQuery) use ($searchValue) {
                              $folioQuery->where('folio', 'LIKE', "%{$searchValue}%")
                                        ->orWhere('solicitante', 'LIKE', "%{$searchValue}%")
                                        ->orWhere('apellido_paterno', 'LIKE', "%{$searchValue}%")
                                        ->orWhere('apellido_materno', 'LIKE', "%{$searchValue}%");
                          });
                    });
                }
            })
            ->rawColumns(['acciones', 'detalles'])
            ->make(true);
    }

    public function getContadores(Request $request)
    {
        try {
            $query = Plano::query();

            // Aplicar todos los filtros incluido búsqueda global
            $this->applyFilters($query, $request);

            // Aplicar filtro de búsqueda global si existe
            if ($request->has('search') && !empty($request->search)) {
                $searchValue = $request->search;
                $query->where(function($q) use ($searchValue) {
                    $q->where('numero_plano', 'LIKE', "%{$searchValue}%")
                      ->orWhere('numero_correlativo', 'LIKE', "%{$searchValue}%")
                      ->orWhere('comuna', 'LIKE', "%{$searchValue}%")
                      ->orWhere('responsable', 'LIKE', "%{$searchValue}%")
                      ->orWhere('proyecto', 'LIKE', "%{$searchValue}%")
                      ->orWhere('mes', 'LIKE', "%{$searchValue}%")
                      ->orWhere('ano', 'LIKE', "%{$searchValue}%")
                      ->orWhere('observaciones', 'LIKE', "%{$searchValue}%")
                      ->orWhere('archivo', 'LIKE', "%{$searchValue}%")
                      ->orWhere('tubo', 'LIKE', "%{$searchValue}%")
                      ->orWhere('tela', 'LIKE', "%{$searchValue}%")
                      ->orWhere('archivo_digital', 'LIKE', "%{$searchValue}%")
                      ->orWhereHas('folios', function($folioQuery) use ($searchValue) {
                          $folioQuery->where('folio', 'LIKE', "%{$searchValue}%")
                                    ->orWhere('solicitante', 'LIKE', "%{$searchValue}%")
                                    ->orWhere('apellido_paterno', 'LIKE', "%{$searchValue}%")
                                    ->orWhere('apellido_materno', 'LIKE', "%{$searchValue}%");
                      });
                });
            }

            // Contar planos
            $totalPlanos = $query->count();

            // Contar folios de esos planos
            $planosIds = $query->pluck('id');
            $totalFolios = PlanoFolio::whereIn('plano_id', $planosIds)->count();

            return response()->json([
                'totalPlanos' => $totalPlanos,
                'totalFolios' => $totalFolios,
                'message' => $totalPlanos . ' plano' . ($totalPlanos != 1 ? 's' : '') .
                            ' encontrado' . ($totalPlanos != 1 ? 's' : '') .
                            ' con ' . $totalFolios . ' folio' . ($totalFolios != 1 ? 's' : '')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error: ' . $e->getMessage(),
                'totalPlanos' => 0,
                'totalFolios' => 0
            ], 500);
        }
    }

    private function applyFilters($query, Request $request)
    {
        // Solo aplicar filtros específicos (no la búsqueda global - eso lo maneja Yajra automáticamente)

        if ($request->filled('comuna')) {
            $query->where('comuna', $request->comuna);
        }

        if ($request->filled('ano')) {
            $query->where('ano', $request->ano);
        }

        if ($request->filled('mes')) {
            $query->where('mes', $request->mes);
        }

        if ($request->filled('responsable')) {
            $query->where('responsable', $request->responsable);
        }

        if ($request->filled('proyecto')) {
            $query->where('proyecto', $request->proyecto);
        }

        if ($request->filled('folio')) {
            $query->whereHas('folios', function($q) use ($request) {
                $q->where('folio', 'LIKE', '%' . $request->folio . '%');
            });
        }

        if ($request->filled('solicitante')) {
            $query->whereHas('folios', function($q) use ($request) {
                $q->where('solicitante', 'LIKE', '%' . $request->solicitante . '%');
            });
        }

        if ($request->filled('apellido_paterno')) {
            $query->whereHas('folios', function($q) use ($request) {
                $q->where('apellido_paterno', 'LIKE', '%' . $request->apellido_paterno . '%');
            });
        }

        if ($request->filled('apellido_materno')) {
            $query->whereHas('folios', function($q) use ($request) {
                $q->where('apellido_materno', 'LIKE', '%' . $request->apellido_materno . '%');
            });
        }

        if ($request->filled('numero_plano')) {
            $query->where('numero_plano', 'LIKE', '%' . $request->numero_plano . '%');
        }

        if ($request->filled('archivo')) {
            $query->where('archivo', 'LIKE', '%' . $request->archivo . '%');
        }

        if ($request->filled('tubo')) {
            $query->where('tubo', 'LIKE', '%' . $request->tubo . '%');
        }

        if ($request->filled('tela')) {
            $query->where('tela', 'LIKE', '%' . $request->tela . '%');
        }

        if ($request->filled('archivo_digital')) {
            $query->where('archivo_digital', 'LIKE', '%' . $request->archivo_digital . '%');
        }

        if ($request->filled('observaciones')) {
            $query->where('observaciones', 'LIKE', '%' . $request->observaciones . '%');
        }

        if ($request->filled('hectareas_min')) {
            $query->where('total_hectareas', '>=', $request->hectareas_min);
        }

        if ($request->filled('hectareas_max')) {
            $query->where('total_hectareas', '<=', $request->hectareas_max);
        }

        if ($request->filled('m2_min')) {
            $query->where('total_m2', '>=', $request->m2_min);
        }

        if ($request->filled('m2_max')) {
            $query->where('total_m2', '<=', $request->m2_max);
        }
    }

    private function getSolicitanteDisplay($plano)
    {
        $folios = $plano->folios;
        if ($folios->isEmpty()) return '-';

        $solicitantes = $folios->pluck('solicitante')->unique()->filter();
        if ($solicitantes->count() > 1) {
            return 'MÚLTIPLES';
        }
        return $solicitantes->first() ?: '-';
    }

    private function getApellidoPaternoDisplay($plano)
    {
        $folios = $plano->folios;
        if ($folios->isEmpty()) return '-';

        $apellidos = $folios->pluck('apellido_paterno')->unique()->filter();
        if ($apellidos->count() > 1) {
            return '-';
        }
        return $apellidos->first() ?: '-';
    }

    private function getApellidoMaternoDisplay($plano)
    {
        $folios = $plano->folios;
        if ($folios->isEmpty()) return '-';

        $apellidos = $folios->pluck('apellido_materno')->unique()->filter();
        if ($apellidos->count() > 1) {
            return '-';
        }
        return $apellidos->first() ?: '-';
    }

    private function formatNumeroPlanoCompleto($plano)
    {
        // Formato: codigo_region + codigo_comuna + numero_correlativo + tipo_saneamiento
        // Ejemplo: 08 + 301 + 29800 + SU = 0830129800SU

        $codigoRegion = str_pad($plano->codigo_region ?: '08', 2, '0', STR_PAD_LEFT);
        $codigoComuna = str_pad($plano->codigo_comuna ?: '', 3, '0', STR_PAD_LEFT);
        $numeroCorrelativo = $plano->numero_correlativo ?: $plano->numero_plano;
        $tipoSaneamiento = $plano->tipo_saneamiento ?: '';

        return $codigoRegion . $codigoComuna . $numeroCorrelativo . $tipoSaneamiento;
    }

    private function generarHtmlDetallesCompletos($plano)
    {
        $html = '
        <div class="row">
            <!-- Sección PLANO -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-map"></i> Información del Plano
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>N° Plano:</strong></td>
                                <td>' . ($plano->numero_plano ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>N° Completo:</strong></td>
                                <td>' . $this->formatNumeroPlanoCompleto($plano) . '</td>
                            </tr>
                            <tr>
                                <td><strong>Tipo:</strong></td>
                                <td>' . ($plano->tipo_saneamiento ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Comuna:</strong></td>
                                <td>' . ($plano->comuna ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Provincia:</strong></td>
                                <td>' . ($plano->provincia ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Mes:</strong></td>
                                <td>' . ($plano->mes ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Año:</strong></td>
                                <td>' . ($plano->ano ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Responsable:</strong></td>
                                <td>' . ($plano->responsable ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Proyecto:</strong></td>
                                <td>' . ($plano->proyecto ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Providencia:</strong></td>
                                <td>' . ($plano->providencia ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Archivo:</strong></td>
                                <td>' . ($plano->archivo ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Tubo:</strong></td>
                                <td>' . ($plano->tubo ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Tela:</strong></td>
                                <td>' . ($plano->tela ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Archivo Digital:</strong></td>
                                <td>' . ($plano->archivo_digital ?: '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Total Hectáreas:</strong></td>
                                <td>' . ($plano->total_hectareas ? number_format($plano->total_hectareas, 2) : '-') . '</td>
                            </tr>
                            <tr>
                                <td><strong>Total M²:</strong></td>
                                <td>' . number_format($plano->total_m2 ?: 0) . '</td>
                            </tr>
                            <tr>
                                <td><strong>Cantidad Folios:</strong></td>
                                <td>' . $plano->cantidad_folios . '</td>
                            </tr>
                        </table>';

        if ($plano->observaciones) {
            $html .= '
                        <div class="mt-3">
                            <strong>Observaciones:</strong>
                            <div class="bg-light p-2 rounded mt-1">
                                ' . nl2br(e($plano->observaciones)) . '
                            </div>
                        </div>';
        }

        $html .= '
                    </div>
                </div>
            </div>

            <!-- Sección FOLIOS -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list"></i> Folios (' . $plano->cantidad_folios . ')
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Solicitante</th>
                                        <th>Ap. Paterno</th>
                                        <th>Ap. Materno</th>
                                        <th>Tipo</th>
                                        <th>N°</th>
                                        <th>Hectáreas</th>
                                        <th>M²</th>
                                    </tr>
                                </thead>
                                <tbody>';

        foreach ($plano->folios as $folio) {
            $html .= '
                                    <tr>
                                        <td>' . ($folio->folio ?: '-') . '</td>
                                        <td>' . ($folio->solicitante ?: '-') . '</td>
                                        <td>' . ($folio->apellido_paterno ?: '-') . '</td>
                                        <td>' . ($folio->apellido_materno ?: '-') . '</td>
                                        <td>
                                            <span class="badge badge-' . ($folio->tipo_inmueble == 'HIJUELA' ? 'info' : 'warning') . '">
                                                ' . $folio->tipo_inmueble . '
                                            </span>
                                        </td>
                                        <td>' . ($folio->numero_inmueble ?: '-') . '</td>
                                        <td>' . ($folio->hectareas ? number_format($folio->hectareas, 2) : '-') . '</td>
                                        <td>' . number_format($folio->m2 ?: 0) . '</td>
                                    </tr>';
        }

        $html .= '
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    public function getFoliosExpansion($planoId)
    {
        $plano = Plano::with('folios')->findOrFail($planoId);

        $html = '';
        foreach ($plano->folios as $folio) {
            $html .= '<tr class="child-row bg-light">';
            // Columna EDITAR - Botón para editar folio individual
            if (Auth::user()->isRegistro()) {
                $html .= '<td class="text-center"><button class="btn btn-sm btn-primary editar-folio" data-folio-id="' . $folio->id . '" title="Editar folio"><i class="fas fa-edit"></i></button></td>';
            } else {
                $html .= '<td></td>';
            }
            $html .= '<td class="pl-4">└ Folio</td>'; // Columna REASIGNAR -> muestra "└ Folio"
            $html .= '<td>' . $folio->folio . '</td>'; // Columna N° PLANO -> muestra folio individual
            $html .= '<td>' . ($folio->solicitante ?: '-') . '</td>'; // Columna FOLIOS -> muestra solicitante
            $html .= '<td>' . ($folio->apellido_paterno ?: '-') . '</td>'; // Columna SOLICITANTE -> muestra apellido paterno
            $html .= '<td>' . ($folio->apellido_materno ?: '-') . '</td>'; // Columna APELLIDO PATERNO -> muestra apellido materno
            $html .= '<td></td>'; // Columna APELLIDO MATERNO -> vacía
            $html .= '<td></td>'; // Columna COMUNA -> vacía
            $html .= '<td>' . ($folio->hectareas ? number_format($folio->hectareas, 2) : '-') . '</td>'; // Columna HECTÁREAS
            $html .= '<td>' . number_format($folio->m2 ?: 0) . '</td>'; // Columna M²
            $html .= '<td colspan="12"></td>'; // Resto vacío - ajustado para las nuevas columnas
            $html .= '</tr>';
        }

        return response()->json(['html' => $html]);
    }

    public function getDetallesCompletos($planoId)
    {
        $plano = Plano::with(['folios', 'creator'])->findOrFail($planoId);

        $html = $this->generarHtmlDetallesCompletos($plano);

        return response()->json([
            'success' => true,
            'plano' => [
                'numero_plano_completo' => $this->formatNumeroPlanoCompleto($plano)
            ],
            'html' => $html
        ]);
    }

    private function getDisplayFolios($plano)
    {
        $folios = $plano->folios;
        $count = $folios->count();

        if ($count == 0) {
            return '-';
        }

        if ($count <= 2) {
            return $folios->pluck('folio')->filter()->join(', ') ?: 'S/F';
        }

        $first_two = $folios->take(2)->pluck('folio')->filter()->join(', ');
        $remaining = $count - 2;
        return $first_two . " +{$remaining} más";
    }

    public function show($id)
    {
        $plano = Plano::with(['folios', 'creator'])->findOrFail($id);
        return view('admin.planos.show', compact('plano'));
    }

    public function edit($id)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para editar planos');
        }

        $plano = Plano::with('folios')->findOrFail($id);
        $comunas = ComunaBiobio::getParaSelect();

        return response()->json([
            'plano' => $plano,
            'comunas' => $comunas
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para editar planos');
        }

        $request->validate([
            'comuna' => 'required|string|max:100',
            'responsable' => 'required|string|max:255',
            'proyecto' => 'required|string|max:255',
            'tipo_saneamiento' => 'required|in:SR,SU,CR,CU',
            'provincia' => 'required|string|max:100',
            'mes' => 'required|string|max:20',
            'ano' => 'required|integer|between:2020,2030',
            'total_hectareas' => 'nullable|numeric|min:0',
            'total_m2' => 'required|integer|min:1',
            'observaciones' => 'nullable|string|max:1000',
            'archivo' => 'nullable|string|max:255',
            'tubo' => 'nullable|string|max:255',
            'tela' => 'nullable|string|max:255',
            'archivo_digital' => 'nullable|string|max:255',
        ]);

        $plano = Plano::findOrFail($id);
        $plano->update($request->only([
            'comuna', 'responsable', 'proyecto', 'tipo_saneamiento',
            'provincia', 'mes', 'ano', 'total_hectareas', 'total_m2',
            'observaciones', 'archivo', 'tubo', 'tela', 'archivo_digital'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Plano actualizado correctamente'
        ]);
    }

    public function reasignar(Request $request, $id)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para reasignar planos');
        }

        $request->validate([
            'nuevo_numero' => 'required|string|max:50|unique:planos,numero_plano,' . $id
        ]);

        $plano = Plano::findOrFail($id);
        $plano->update([
            'numero_plano' => $request->nuevo_numero
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Número de plano reasignado correctamente'
        ]);
    }

    public function destroy($id)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para eliminar planos');
        }

        $plano = Plano::findOrFail($id);
        $plano->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plano eliminado correctamente'
        ]);
    }

    public function editFolio($folioId)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para editar folios');
        }

        $folio = PlanoFolio::findOrFail($folioId);

        return response()->json([
            'folio' => $folio
        ]);
    }

    public function updateFolio(Request $request, $folioId)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para editar folios');
        }

        $request->validate([
            'folio' => 'nullable|string|max:50',
            'solicitante' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'tipo_inmueble' => 'required|in:HIJUELA,SITIO',
            'numero_inmueble' => 'nullable|integer|min:1',
            'hectareas' => 'nullable|numeric|min:0',
            'm2' => 'required|integer|min:1',
            'matrix_folio' => 'nullable|string|max:50',
            'is_from_matrix' => 'required|boolean',
        ]);

        $folio = PlanoFolio::findOrFail($folioId);

        // Si cambia el tipo de inmueble a SITIO, limpiar hectáreas
        $data = $request->all();
        if ($data['tipo_inmueble'] === 'SITIO') {
            $data['hectareas'] = null;
        }

        $folio->update($data);

        // Recalcular totales del plano padre
        $this->recalcularTotalesPlano($folio->plano_id);

        return response()->json([
            'success' => true,
            'message' => 'Folio actualizado correctamente'
        ]);
    }

    private function recalcularTotalesPlano($planoId)
    {
        $plano = Plano::with('folios')->findOrFail($planoId);

        $totalHectareas = $plano->folios->sum('hectareas');
        $totalM2 = $plano->folios->sum('m2');
        $cantidadFolios = $plano->folios->count();

        $plano->update([
            'total_hectareas' => $totalHectareas > 0 ? $totalHectareas : null,
            'total_m2' => $totalM2,
            'cantidad_folios' => $cantidadFolios
        ]);
    }
}