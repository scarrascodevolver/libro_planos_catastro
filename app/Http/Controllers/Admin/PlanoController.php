<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plano;
use App\Models\PlanoFolio;
use App\Models\ComunaBiobio;
use App\Models\User;
use App\Models\SessionControl;
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

        // Relación comuna => provincia para select dependiente
        $comunasProvincia = ComunaBiobio::pluck('provincia', 'nombre')->toArray();

        // Provincias únicas para el select
        $provincias = ComunaBiobio::select('provincia')->distinct()->orderBy('provincia')->pluck('provincia');

        $responsables = Plano::select('responsable')->distinct()->whereNotNull('responsable')->orderBy('responsable')->pluck('responsable');
        $proyectos = Plano::select('proyecto')->distinct()->whereNotNull('proyecto')->orderBy('proyecto')->pluck('proyecto');
        $anos = Plano::select('ano')->distinct()->whereNotNull('ano')->where('ano', '>', 0)->orderBy('ano', 'desc')->pluck('ano')->unique();

        return view('admin.planos.index', compact('comunas', 'comunasProvincia', 'provincias', 'responsables', 'proyectos', 'anos'));
    }

    private function getDataTableData(Request $request)
    {
        $query = Plano::with(['folios' => function($query) {
            $query->orderBy('id');
        }])
        ->orderBy('numero_correlativo', 'desc'); // Ordenar por correlativo descendente (más reciente primero)

        // Aplicar filtros
        $this->applyFilters($query, $request);

        return DataTables::of($query)
            ->addColumn('acciones', function ($plano) {
                // Verificar si usuario tiene control de sesión
                $tieneControl = false;
                if (Auth::user()->isRegistro()) {
                    $controlHolder = SessionControl::quienTieneControl();
                    $tieneControl = $controlHolder && $controlHolder->id === Auth::id();
                }

                $acciones = '<div class="dropdown">';
                $acciones .= '<button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">';
                $acciones .= '<i class="fas fa-cog"></i> Acciones';
                $acciones .= '</button>';
                $acciones .= '<div class="dropdown-menu">';
                $acciones .= '<a class="dropdown-item ver-detalles" href="#" data-id="'.$plano->id.'"><i class="fas fa-eye mr-2 text-info"></i>Ver Detalles</a>';

                if (Auth::user()->isRegistro()) {
                    // Editar plano siempre disponible para rol registro
                    $acciones .= '<a class="dropdown-item editar-plano" href="#" data-id="'.$plano->id.'"><i class="fas fa-edit mr-2 text-primary"></i>Editar Plano</a>';

                    // Reasignar y Eliminar requieren control de sesión
                    if ($tieneControl) {
                        $acciones .= '<a class="dropdown-item reasignar-plano" href="#" data-id="'.$plano->id.'"><i class="fas fa-exchange-alt mr-2 text-warning"></i>Reasignar N°</a>';
                        $acciones .= '<div class="dropdown-divider"></div>';
                        $acciones .= '<a class="dropdown-item eliminar-plano" href="#" data-id="'.$plano->id.'" data-numero="'.$plano->numero_plano_completo.'" data-folios="'.$plano->folios->count().'"><i class="fas fa-trash-alt mr-2 text-danger"></i>Eliminar Plano</a>';
                    } else {
                        // Botones deshabilitados con tooltip
                        $acciones .= '<a class="dropdown-item disabled text-muted" href="#" title="Requiere control de sesión"><i class="fas fa-exchange-alt mr-2"></i>Reasignar N° <i class="fas fa-lock ml-1"></i></a>';
                        $acciones .= '<div class="dropdown-divider"></div>';
                        $acciones .= '<a class="dropdown-item disabled text-muted" href="#" title="Requiere control de sesión"><i class="fas fa-trash-alt mr-2"></i>Eliminar Plano <i class="fas fa-lock ml-1"></i></a>';
                    }
                }

                $acciones .= '</div></div>';
                return $acciones;
            })
            ->addColumn('folios_display', function ($plano) {
                return $this->getDisplayFolios($plano);
            })
            ->addColumn('folios_completos', function ($plano) {
                // Columna oculta para exportación Excel con TODOS los folios
                return $plano->folios->pluck('folio')->filter()->join(', ') ?: '-';
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
                return number_format($plano->total_m2 ?: 0, 2, ',', '.');
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
                return $plano->providencia_archivo ?: '-';
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
                          ->orWhere('providencia_archivo', 'LIKE', "%{$searchValue}%")
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
            ->rawColumns(['acciones'])
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
                      ->orWhere('providencia_archivo', 'LIKE', "%{$searchValue}%")
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
            $query->where('providencia_archivo', 'LIKE', '%' . $request->archivo . '%');
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
                                <td><strong>Providencia Archivo:</strong></td>
                                <td>' . ($plano->providencia_archivo ?: '-') . '</td>
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
                                <td>' . number_format($plano->total_m2 ?: 0, 2, ',', '.') . '</td>
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

            <!-- Sección FOLIOS/HIJUELAS/SITIOS -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list"></i> Detalle de Inmuebles (' . $plano->cantidad_folios . ')
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 120px;">Tipo</th>
                                        <th>Folio</th>
                                        <th>Solicitante</th>
                                        <th class="text-right">Hectáreas</th>
                                        <th class="text-right">M²</th>
                                    </tr>
                                </thead>
                                <tbody>';

        foreach ($plano->folios as $folio) {
            $tipoLabel = $folio->tipo_inmueble ?: 'HIJUELA';
            $badgeColor = $tipoLabel == 'HIJUELA' ? 'info' : 'warning';
            $nombreCompleto = trim(($folio->solicitante ?: '') . ' ' . ($folio->apellido_paterno ?: '') . ' ' . ($folio->apellido_materno ?: ''));

            // Verificar si tiene desglose de inmuebles
            $inmuebles = $folio->inmuebles;

            if ($inmuebles->count() > 0) {
                // Mostrar cada inmueble por separado
                foreach ($inmuebles as $inmueble) {
                    $hectareasDisplay = $inmueble->hectareas
                        ? number_format($inmueble->hectareas, 4, ',', '.') . ' ha'
                        : '-';
                    $m2Display = number_format($inmueble->m2 ?: 0, 0, ',', '.') . ' m²';

                    $html .= '
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge badge-' . $badgeColor . '">
                                                ' . $inmueble->tipo_inmueble . ' #' . $inmueble->numero_inmueble . '
                                            </span>
                                        </td>
                                        <td><strong>' . ($folio->folio ?: '-') . '</strong></td>
                                        <td>' . ($nombreCompleto ?: '-') . '</td>
                                        <td class="text-right">' . $hectareasDisplay . '</td>
                                        <td class="text-right"><strong>' . $m2Display . '</strong></td>
                                    </tr>';
                }
            } else {
                // Sin desglose - mostrar solo el folio con totales
                $numero = $folio->numero_inmueble ? " ({$folio->numero_inmueble})" : '';
                $hectareasDisplay = $folio->hectareas
                    ? number_format($folio->hectareas, 4, ',', '.') . ' ha'
                    : '-';
                $m2Display = number_format($folio->m2 ?: 0, 0, ',', '.') . ' m²';

                $html .= '
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge badge-' . $badgeColor . '">
                                                ' . $tipoLabel . $numero . '
                                            </span>
                                        </td>
                                        <td><strong>' . ($folio->folio ?: '-') . '</strong></td>
                                        <td>' . ($nombreCompleto ?: '-') . '</td>
                                        <td class="text-right">' . $hectareasDisplay . '</td>
                                        <td class="text-right"><strong>' . $m2Display . '</strong></td>
                                    </tr>';
            }
        }

        // Fila de totales
        $totalHa = $plano->total_hectareas
            ? number_format($plano->total_hectareas, 4, ',', '.') . ' ha'
            : '-';
        $totalM2 = number_format($plano->total_m2 ?: 0, 0, ',', '.') . ' m²';

        $html .= '
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th colspan="3" class="text-right">TOTALES:</th>
                                        <th class="text-right">' . $totalHa . '</th>
                                        <th class="text-right"><strong>' . $totalM2 . '</strong></th>
                                    </tr>
                                </tfoot>
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
            $html .= '<td>' . ($folio->hectareas ? number_format($folio->hectareas, 2, ',', '') : '-') . '</td>'; // Columna HECTÁREAS
            $html .= '<td>' . number_format($folio->m2 ?: 0, 2, ',', '.') . '</td>'; // Columna M²
            $html .= '<td colspan="11"></td>'; // Resto vacío - ajustado para columnas (sin detalles)
            $html .= '</tr>';
        }

        return response()->json(['html' => $html]);
    }

    public function getDetallesCompletos($planoId)
    {
        $plano = Plano::with(['folios.inmuebles', 'creator'])->findOrFail($planoId);

        $html = $this->generarHtmlDetallesCompletos($plano);

        return response()->json([
            'success' => true,
            'plano' => [
                'id' => $plano->id,
                'numero_plano' => $plano->numero_plano,
                'numero_plano_completo' => $this->formatNumeroPlanoCompleto($plano),
                'codigo_comuna' => $plano->codigo_comuna,
                'tipo_saneamiento' => $plano->tipo_saneamiento
            ],
            'folios' => $plano->folios,
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

        // Agregar campos calculados
        $planoArray = $plano->toArray();
        $planoArray['numero_plano_completo'] = $this->formatNumeroPlanoCompleto($plano);
        $planoArray['cantidad_folios'] = $plano->folios->count();

        return response()->json([
            'plano' => $planoArray,
            'comunas' => $comunas
        ]);
    }

    /**
     * Agregar un nuevo folio al plano
     */
    public function agregarFolio(Request $request, $id)
    {
        if (!Auth::user()->isRegistro()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para agregar folios'
            ], 403);
        }

        $plano = Plano::findOrFail($id);

        // Determinar si es rural o urbano
        $esRural = in_array($plano->tipo_saneamiento, ['SR', 'CR']);

        // Validaciones base
        $rules = [
            'folio' => 'nullable|string|max:50',
            'solicitante' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'tipo_inmueble' => 'required|in:HIJUELA,SITIO',
            'hectareas' => 'nullable|numeric|min:0',
        ];

        // Validación diferenciada de m² según tipo de plano
        if ($esRural) {
            // Rural: m² es opcional
            $rules['m2'] = 'nullable|numeric|min:0';
        } else {
            // Urbano: m² es obligatorio
            $rules['m2'] = 'required|numeric|min:1';
        }

        $request->validate($rules);

        // Validación adicional para rurales: al menos hectáreas O m² debe estar presente
        if ($esRural) {
            $hectareas = $request->input('hectareas');
            $m2 = $request->input('m2');

            $tieneHectareas = !empty($hectareas) && $hectareas > 0;
            $tieneM2 = !empty($m2) && $m2 > 0;

            if (!$tieneHectareas && !$tieneM2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para planos rurales debe completar al menos Hectáreas o M²'
                ], 422);
            }
        }

        // Crear el nuevo folio
        $folio = PlanoFolio::create([
            'plano_id' => $plano->id,
            'folio' => $request->folio,
            'solicitante' => $request->solicitante,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'tipo_inmueble' => $request->tipo_inmueble,
            'hectareas' => $request->tipo_inmueble === 'HIJUELA' ? $request->hectareas : null,
            'm2' => $request->m2,
            'is_from_matrix' => false,
        ]);

        // Recalcular totales del plano
        $this->recalcularTotalesPlano($plano);

        return response()->json([
            'success' => true,
            'message' => 'Folio agregado correctamente',
            'folio' => $folio
        ]);
    }

    /**
     * Recalcular totales de hectáreas, m² y cantidad de folios
     */
    private function recalcularTotalesPlano($planoOrId)
    {
        // Aceptar tanto objeto Plano como ID
        if ($planoOrId instanceof Plano) {
            $plano = $planoOrId;
        } else {
            $plano = Plano::findOrFail($planoOrId);
        }

        $folios = $plano->folios()->get();

        $plano->update([
            'cantidad_folios' => $folios->count(),
            'total_hectareas' => $folios->sum('hectareas') ?: null,
            'total_m2' => $folios->sum('m2')
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
            'total_m2' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string|max:1000',
            'providencia_archivo' => 'nullable|string|max:255',
            'tubo' => 'nullable|string|max:255',
            'tela' => 'nullable|string|max:255',
            'archivo_digital' => 'nullable|string|max:255',
        ]);

        $plano = Plano::findOrFail($id);
        $plano->update($request->only([
            'comuna', 'responsable', 'proyecto', 'tipo_saneamiento',
            'provincia', 'mes', 'ano', 'total_hectareas', 'total_m2',
            'observaciones', 'providencia_archivo', 'tubo', 'tela', 'archivo_digital'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Plano actualizado correctamente'
        ]);
    }

    /**
     * Actualizar plano completo con todos sus folios
     */
    public function updateCompleto(Request $request, $id)
    {
        if (!Auth::user()->isRegistro()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para editar planos'
            ], 403);
        }

        // Determinar si es rural o urbano
        $tipoSaneamiento = $request->input('tipo_saneamiento');
        $esRural = in_array($tipoSaneamiento, ['SR', 'CR']);

        // Validaciones base
        $rules = [
            'comuna' => 'required|string|max:100',
            'tipo_saneamiento' => 'required|in:SR,SU,CR,CU',
            'provincia' => 'nullable|string|max:100',
            'responsable' => 'nullable|string|max:255',
            'mes' => 'required|string|max:20',
            'ano' => 'required|integer|between:2000,2030',
            'proyecto' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string|max:1000',
            'folios' => 'required|array|min:1',
            'folios.*.folio' => 'nullable|string|max:50',
            'folios.*.solicitante' => 'nullable|string|max:255',
            'folios.*.apellido_paterno' => 'nullable|string|max:255',
            'folios.*.apellido_materno' => 'nullable|string|max:255',
            'folios.*.tipo_inmueble' => 'required|in:HIJUELA,SITIO',
            'folios.*.hectareas' => 'nullable|numeric|min:0',
        ];

        // Validación diferenciada de m² según tipo de plano
        if ($esRural) {
            // Rural: m² es opcional
            $rules['folios.*.m2'] = 'nullable|numeric|min:0';
        } else {
            // Urbano: m² es obligatorio
            $rules['folios.*.m2'] = 'required|numeric|min:0.01';
        }

        $request->validate($rules);

        // Validación adicional para rurales: cada folio debe tener al menos hectáreas O m²
        if ($esRural) {
            $folios = $request->input('folios', []);
            foreach ($folios as $index => $folio) {
                $hectareas = $folio['hectareas'] ?? null;
                $m2 = $folio['m2'] ?? null;

                $tieneHectareas = !empty($hectareas) && $hectareas > 0;
                $tieneM2 = !empty($m2) && $m2 > 0;

                if (!$tieneHectareas && !$tieneM2) {
                    return response()->json([
                        'success' => false,
                        'message' => "Folio #" . ($index + 1) . ": Para planos rurales debe completar al menos Hectáreas o M²"
                    ], 422);
                }
            }
        }

        $plano = Plano::with('folios')->findOrFail($id);

        try {
            DB::beginTransaction();

            // Si cambió la comuna, actualizar codigo_comuna y numero_plano
            $nuevoComunaNombre = $request->comuna;
            $comunaAnterior = $plano->comuna;
            $actualizarNumero = false;

            if ($nuevoComunaNombre !== $comunaAnterior) {
                // Buscar código de la nueva comuna
                $nuevaComuna = ComunaBiobio::where('nombre', $nuevoComunaNombre)->first();
                if ($nuevaComuna) {
                    $plano->codigo_comuna = $nuevaComuna->codigo;

                    // Reconstruir número de plano
                    $codigoRegion = str_pad($plano->codigo_region ?: '08', 2, '0', STR_PAD_LEFT);
                    $codigoComuna = str_pad($nuevaComuna->codigo, 3, '0', STR_PAD_LEFT);
                    $correlativo = $plano->numero_correlativo;
                    $tipo = $request->tipo_saneamiento;

                    $plano->numero_plano = $codigoRegion . $codigoComuna . $correlativo . $tipo;
                    $actualizarNumero = true;
                }
            }

            // Actualizar datos del plano (convertir null a empty string para campos NOT NULL)
            $plano->update([
                'comuna' => $request->comuna,
                'tipo_saneamiento' => $request->tipo_saneamiento,
                'provincia' => $request->provincia ?: '',
                'responsable' => $request->responsable ?: '',
                'mes' => $request->mes,
                'ano' => $request->ano,
                'proyecto' => $request->proyecto ?: '',
                'observaciones' => $request->observaciones ?: '',
            ]);

            // Obtener IDs de folios existentes
            $foliosExistentesIds = $plano->folios->pluck('id')->toArray();
            $foliosRecibidosIds = [];

            // Procesar cada folio recibido
            foreach ($request->folios as $folioData) {
                $folioId = isset($folioData['id']) && $folioData['id'] ? $folioData['id'] : null;

                // Preparar datos del folio
                $datosfolio = [
                    'folio' => $folioData['folio'] ?: null,
                    'solicitante' => $folioData['solicitante'] ?: '',
                    'apellido_paterno' => $folioData['apellido_paterno'] ?: null,
                    'apellido_materno' => $folioData['apellido_materno'] ?: null,
                    'tipo_inmueble' => $folioData['tipo_inmueble'],
                    'hectareas' => $folioData['tipo_inmueble'] === 'HIJUELA' ? ($folioData['hectareas'] ?: null) : null,
                    'm2' => !empty($folioData['m2']) ? $folioData['m2'] : 0,
                    'is_from_matrix' => false,
                ];

                if ($folioId && in_array($folioId, $foliosExistentesIds)) {
                    // Actualizar folio existente
                    PlanoFolio::where('id', $folioId)->update($datosfolio);
                    $foliosRecibidosIds[] = $folioId;
                } else {
                    // Crear nuevo folio
                    $nuevoFolio = PlanoFolio::create(array_merge($datosfolio, [
                        'plano_id' => $plano->id
                    ]));
                    $foliosRecibidosIds[] = $nuevoFolio->id;
                }
            }

            // Eliminar folios que ya no están (fueron quitados en la UI)
            $foliosAEliminar = array_diff($foliosExistentesIds, $foliosRecibidosIds);
            if (!empty($foliosAEliminar)) {
                PlanoFolio::whereIn('id', $foliosAEliminar)->delete();
            }

            // Recalcular totales
            $this->recalcularTotalesPlano($plano);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plano y folios actualizados correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reasignar(Request $request, $id)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para reasignar planos');
        }

        $request->validate([
            'nuevo_numero' => 'required|string|max:50|unique:planos,numero_plano'
        ]);

        $planoOriginal = Plano::with('folios')->findOrFail($id);
        $numeroAnterior = $planoOriginal->numero_plano;
        $nuevoNumero = $request->nuevo_numero;

        // Extraer el nuevo correlativo del número
        $nuevoCorrelativo = null;
        if (strlen($nuevoNumero) >= 7) {
            $sinPrefijo = substr($nuevoNumero, 5);
            $sinTipo = substr($sinPrefijo, 0, -2);
            $nuevoCorrelativo = intval($sinTipo);
        }

        // Crear nuevo plano copiando todos los datos del original
        $nuevoPlano = Plano::create([
            'numero_plano' => $nuevoNumero,
            'codigo_region' => $planoOriginal->codigo_region,
            'codigo_comuna' => $planoOriginal->codigo_comuna,
            'numero_correlativo' => $nuevoCorrelativo,
            'tipo_saneamiento' => $planoOriginal->tipo_saneamiento,
            'provincia' => $planoOriginal->provincia,
            'comuna' => $planoOriginal->comuna,
            'mes' => $planoOriginal->mes,
            'ano' => $planoOriginal->ano,
            'responsable' => $planoOriginal->responsable,
            'proyecto' => $planoOriginal->proyecto,
            'providencia' => $planoOriginal->providencia,
            'total_hectareas' => $planoOriginal->total_hectareas,
            'total_m2' => $planoOriginal->total_m2,
            'cantidad_folios' => $planoOriginal->cantidad_folios,
            'observaciones' => $planoOriginal->observaciones,
            'providencia_archivo' => $planoOriginal->providencia_archivo,
            'tubo' => $planoOriginal->tubo,
            'tela' => $planoOriginal->tela,
            'archivo_digital' => $planoOriginal->archivo_digital,
            'created_by' => Auth::id()
        ]);

        // Copiar todos los folios al nuevo plano
        foreach ($planoOriginal->folios as $folioOriginal) {
            PlanoFolio::create([
                'plano_id' => $nuevoPlano->id,
                'folio' => $folioOriginal->folio,
                'solicitante' => $folioOriginal->solicitante,
                'apellido_paterno' => $folioOriginal->apellido_paterno,
                'apellido_materno' => $folioOriginal->apellido_materno,
                'tipo_inmueble' => $folioOriginal->tipo_inmueble,
                'numero_inmueble' => $folioOriginal->numero_inmueble,
                'hectareas' => $folioOriginal->hectareas,
                'm2' => $folioOriginal->m2,
                'is_from_matrix' => $folioOriginal->is_from_matrix,
                'matrix_folio' => $folioOriginal->matrix_folio
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Plano reasignado: {$numeroAnterior} → {$nuevoNumero}",
            'nuevo_plano_id' => $nuevoPlano->id,
            'numero_anterior' => $numeroAnterior,
            'numero_nuevo' => $nuevoNumero
        ]);
    }

    public function destroy($id)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para eliminar planos');
        }

        // Verificar control de sesión
        $controlHolder = SessionControl::quienTieneControl();
        $tieneControl = $controlHolder && $controlHolder->id === Auth::id();

        if (!$tieneControl) {
            return response()->json([
                'success' => false,
                'message' => 'Solo el usuario con control de sesión puede eliminar planos. ' .
                            ($controlHolder ? 'Control actual: ' . $controlHolder->name : 'Nadie tiene control.')
            ], 403);
        }

        try {
            // Cargar plano con sus folios
            $plano = Plano::with('folios')->findOrFail($id);

            // Capturar información ANTES de eliminar
            $numeroPlano = $plano->numero_plano_completo;
            $cantidadFolios = $plano->folios->count();
            $foliosEliminados = $plano->folios->pluck('folio')->toArray();

            // Logging crítico para auditoría
            \Log::warning('ELIMINACIÓN DE PLANO', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'plano_id' => $plano->id,
                'numero_plano' => $numeroPlano,
                'comuna' => $plano->comuna,
                'cantidad_folios' => $cantidadFolios,
                'folios' => implode(', ', $foliosEliminados),
                'timestamp' => now()
            ]);

            // Eliminar plano (CASCADE elimina folios automáticamente)
            $plano->delete();

            return response()->json([
                'success' => true,
                'message' => "Plano {$numeroPlano} eliminado correctamente",
                'plano_eliminado' => $numeroPlano,
                'folios_eliminados' => $cantidadFolios
            ]);

        } catch (\Exception $e) {
            \Log::error('ERROR AL ELIMINAR PLANO', [
                'user_id' => Auth::id(),
                'plano_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el plano: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editFolio($folioId)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para editar folios');
        }

        $folio = PlanoFolio::with('plano')->findOrFail($folioId);

        // Determinar si es rural o urbano
        $esRural = in_array($folio->plano->tipo_saneamiento, ['SR', 'CR']);

        return response()->json([
            'success' => true,
            'folio' => $folio,
            'plano' => [
                'tipo_saneamiento' => $folio->plano->tipo_saneamiento,
                'es_rural' => $esRural
            ]
        ]);
    }

    public function updateFolio(Request $request, $folioId)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para editar folios');
        }

        $folio = PlanoFolio::findOrFail($folioId);
        $plano = $folio->plano;

        // Determinar si es rural o urbano desde el plano padre
        $esRural = in_array($plano->tipo_saneamiento, ['SR', 'CR']);

        // Validaciones base
        $rules = [
            'folio' => 'nullable|string|max:50',
            'solicitante' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'tipo_inmueble' => 'required|in:HIJUELA,SITIO',
            'numero_inmueble' => 'nullable|integer|min:1',
            'hectareas' => 'nullable|numeric|min:0',
            'matrix_folio' => 'nullable|string|max:50',
            'is_from_matrix' => 'required|boolean',
        ];

        // Validación diferenciada para m² según tipo de plano
        if ($esRural) {
            // Rural: m² es opcional, pero al menos hectáreas o m² debe estar presente
            $rules['m2'] = 'nullable|numeric|min:0';
        } else {
            // Urbano: m² es obligatorio
            $rules['m2'] = 'required|numeric|min:0.01';
        }

        $request->validate($rules);

        // Validación adicional para rurales: al menos hectáreas O m² debe estar presente
        if ($esRural) {
            $hectareas = $request->input('hectareas');
            $m2 = $request->input('m2');

            $tieneHectareas = !empty($hectareas) && $hectareas > 0;
            $tieneM2 = !empty($m2) && $m2 > 0;

            if (!$tieneHectareas && !$tieneM2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para planos rurales debe completar al menos Hectáreas o M²'
                ], 422);
            }
        }

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

    /**
     * Obtener folios de un plano para gestión (agregar/quitar)
     */
    public function getFoliosParaGestion($id)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para gestionar folios');
        }

        $plano = Plano::with('folios')->findOrFail($id);

        // Determinar si es rural o urbano según tipo_saneamiento
        $tipoPlano = $plano->tipo_saneamiento;
        $esRural = in_array($tipoPlano, ['SR', 'CR']);
        $tipoInmueble = $esRural ? 'HIJUELA' : 'SITIO';

        return response()->json([
            'success' => true,
            'plano' => [
                'id' => $plano->id,
                'numero_plano_completo' => $this->formatNumeroPlanoCompleto($plano),
                'cantidad_folios' => $plano->cantidad_folios,
                'tipo_saneamiento' => $tipoPlano,
                'es_rural' => $esRural,
                'tipo_inmueble' => $tipoInmueble
            ],
            'folios' => $plano->folios->map(function($folio) {
                return [
                    'id' => $folio->id,
                    'folio' => $folio->folio ?: 'S/F',
                    'solicitante' => $folio->solicitante,
                    'apellido_paterno' => $folio->apellido_paterno,
                    'apellido_materno' => $folio->apellido_materno,
                    'tipo_inmueble' => $folio->tipo_inmueble,
                    'numero_inmueble' => $folio->numero_inmueble,
                    'hectareas' => $folio->hectareas,
                    'm2' => $folio->m2,
                    'nombre_completo' => trim($folio->solicitante . ' ' . $folio->apellido_paterno . ' ' . $folio->apellido_materno)
                ];
            })
        ]);
    }

    /**
     * Quitar folios seleccionados de un plano
     */
    public function quitarFolios(Request $request, $id)
    {
        if (!Auth::user()->isRegistro()) {
            abort(403, 'No tienes permisos para quitar folios');
        }

        $request->validate([
            'folios_ids' => 'required|array|min:1',
            'folios_ids.*' => 'required|integer|exists:planos_folios,id'
        ]);

        $plano = Plano::with('folios')->findOrFail($id);

        // Validar que quede al menos 1 folio
        $foliosAEliminar = count($request->folios_ids);
        $foliosTotales = $plano->folios->count();

        if ($foliosAEliminar >= $foliosTotales) {
            return response()->json([
                'success' => false,
                'message' => 'Debe quedar al menos 1 folio en el plano. No se pueden eliminar todos.'
            ], 422);
        }

        // Validar que los folios pertenecen al plano
        $foliosValidos = $plano->folios->pluck('id')->toArray();
        foreach ($request->folios_ids as $folioId) {
            if (!in_array($folioId, $foliosValidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uno o más folios no pertenecen a este plano'
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Eliminar folios
            PlanoFolio::whereIn('id', $request->folios_ids)->delete();

            // Recalcular totales
            $this->recalcularTotalesPlano($id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Folios eliminados correctamente',
                'folios_eliminados' => $foliosAEliminar
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar folios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar planos en formato expandido (1 fila por folio)
     * Para Excel con información completa de cada folio
     */
    public function exportExpandido(Request $request)
    {
        // Obtener query con filtros aplicados (mismo que DataTable)
        $query = Plano::with(['folios' => function($query) {
            $query->orderBy('id');
        }]);

        // Aplicar los mismos filtros que en DataTable
        $this->aplicarFiltros($query, $request);

        $planos = $query->get();

        // Preparar datos expandidos: 1 fila por folio
        $datosExpandidos = [];

        foreach ($planos as $plano) {
            foreach ($plano->folios as $folio) {
                $datosExpandidos[] = [
                    'numero_plano' => $plano->numero_plano,
                    'folio' => $folio->folio ?: '-',
                    'solicitante' => $folio->solicitante ?: '-',
                    'apellido_paterno' => $folio->apellido_paterno ?: '-',
                    'apellido_materno' => $folio->apellido_materno ?: '-',
                    'comuna' => $plano->comuna,
                    'tipo_inmueble' => $folio->tipo_inmueble,
                    'numero_inmueble' => $folio->numero_inmueble ?: '-',
                    'hectareas' => $folio->hectareas ? number_format($folio->hectareas, 4, ',', '.') : '-',
                    'm2' => $folio->m2 ? number_format($folio->m2, 0, ',', '.') : '-',
                    'mes' => $plano->mes,
                    'ano' => $plano->ano,
                    'responsable' => $plano->responsable,
                    'proyecto' => $plano->proyecto
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $datosExpandidos
        ]);
    }

    /**
     * Aplicar filtros a la query (reutilizable)
     */
    private function aplicarFiltros($query, $request)
    {
        // Filtro por comuna
        if ($request->filled('comuna_filter')) {
            $query->where('comuna', $request->comuna_filter);
        }

        // Filtro por año
        if ($request->filled('ano_filter')) {
            $query->where('ano', $request->ano_filter);
        }

        // Filtro por mes
        if ($request->filled('mes_filter')) {
            $query->where('mes', $request->mes_filter);
        }

        // Filtro por responsable
        if ($request->filled('responsable_filter')) {
            $query->where('responsable', $request->responsable_filter);
        }

        // Filtro por proyecto
        if ($request->filled('proyecto_filter')) {
            $query->where('proyecto', $request->proyecto_filter);
        }

        // Filtro por folio (buscar en relación)
        if ($request->filled('folio_filter')) {
            $folioSearch = $request->folio_filter;
            $query->whereHas('folios', function($q) use ($folioSearch) {
                $q->where('folio', 'LIKE', "%{$folioSearch}%");
            });
        }

        // Filtro por solicitante
        if ($request->filled('solicitante_filter')) {
            $solicitanteSearch = $request->solicitante_filter;
            $query->whereHas('folios', function($q) use ($solicitanteSearch) {
                $q->where('solicitante', 'LIKE', "%{$solicitanteSearch}%");
            });
        }

        // Filtro por apellido paterno
        if ($request->filled('ap_paterno_filter')) {
            $apPaternoSearch = $request->ap_paterno_filter;
            $query->whereHas('folios', function($q) use ($apPaternoSearch) {
                $q->where('apellido_paterno', 'LIKE', "%{$apPaternoSearch}%");
            });
        }

        // Filtro por apellido materno
        if ($request->filled('ap_materno_filter')) {
            $apMaternoSearch = $request->ap_materno_filter;
            $query->whereHas('folios', function($q) use ($apMaternoSearch) {
                $q->where('apellido_materno', 'LIKE', "%{$apMaternoSearch}%");
            });
        }

        // Filtros por rango de hectáreas
        if ($request->filled('hectareas_min')) {
            $query->where('total_hectareas', '>=', $request->hectareas_min);
        }
        if ($request->filled('hectareas_max')) {
            $query->where('total_hectareas', '<=', $request->hectareas_max);
        }

        // Filtros por rango de m²
        if ($request->filled('m2_min')) {
            $query->where('total_m2', '>=', $request->m2_min);
        }
        if ($request->filled('m2_max')) {
            $query->where('total_m2', '<=', $request->m2_max);
        }

        return $query;
    }
}