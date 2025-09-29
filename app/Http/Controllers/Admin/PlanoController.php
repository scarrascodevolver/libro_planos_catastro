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
        $anos = Plano::selectRaw('DISTINCT ano')->whereNotNull('ano')->orderBy('ano', 'desc')->pluck('ano');

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
            ->addColumn('expandir', function ($plano) {
                return '<button class="btn btn-sm btn-info expandir-folios" data-id="'.$plano->id.'"><i class="fas fa-plus"></i></button>';
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
            ->rawColumns(['acciones', 'expandir'])
            ->make(true);
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

    public function getFoliosExpansion($planoId)
    {
        $plano = Plano::with('folios')->findOrFail($planoId);

        $html = '';
        foreach ($plano->folios as $folio) {
            $html .= '<tr class="child-row bg-light">';
            $html .= '<td></td>'; // Columna vacía para EDITAR
            $html .= '<td class="pl-4">└ Folio</td>'; // Columna REASIGNAR -> muestra "└ Folio"
            $html .= '<td>' . $folio->folio . '</td>'; // Columna N° PLANO -> muestra folio individual
            $html .= '<td>' . ($folio->solicitante ?: '-') . '</td>'; // Columna FOLIOS -> muestra solicitante
            $html .= '<td>' . ($folio->apellido_paterno ?: '-') . '</td>'; // Columna SOLICITANTE -> muestra apellido paterno
            $html .= '<td>' . ($folio->apellido_materno ?: '-') . '</td>'; // Columna APELLIDO PATERNO -> muestra apellido materno
            $html .= '<td></td>'; // Columna APELLIDO MATERNO -> vacía
            $html .= '<td></td>'; // Columna COMUNA -> vacía
            $html .= '<td>' . ($folio->hectareas ? number_format($folio->hectareas, 2) : '-') . '</td>'; // Columna HECTÁREAS
            $html .= '<td>' . number_format($folio->m2 ?: 0) . '</td>'; // Columna M²
            $html .= '<td colspan="5"></td>'; // Resto vacío (MES, AÑO, RESPONSABLE, PROYECTO, [+/-])
            $html .= '</tr>';
        }

        return response()->json(['html' => $html]);
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
            'observaciones' => 'nullable|string',
            'archivo' => 'nullable|string|max:255',
            'tubo' => 'nullable|string|max:255',
            'tela' => 'nullable|string|max:255',
            'archivo_digital' => 'nullable|string|max:255',
        ]);

        $plano = Plano::findOrFail($id);
        $plano->update($request->only([
            'comuna', 'responsable', 'proyecto', 'observaciones',
            'archivo', 'tubo', 'tela', 'archivo_digital'
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
}