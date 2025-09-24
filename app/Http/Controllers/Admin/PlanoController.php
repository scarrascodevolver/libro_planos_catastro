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

        $comunas = ComunaBiobio::getParaSelect();
        $responsables = Plano::select('responsable')->distinct()->orderBy('responsable')->pluck('responsable');
        $proyectos = Plano::select('proyecto')->distinct()->orderBy('proyecto')->pluck('proyecto');
        $anos = Plano::selectRaw('YEAR(created_at) as ano')->distinct()->orderBy('ano', 'desc')->pluck('ano');

        return view('admin.planos.index', compact('comunas', 'responsables', 'proyectos', 'anos'));
    }

    private function getDataTableData(Request $request)
    {
        $query = Plano::select([
            'planos.*',
            DB::raw('COUNT(planos_folios.id) as cantidad_folios_calc'),
            DB::raw('SUM(planos_folios.hectareas) as total_hectareas_calc'),
            DB::raw('SUM(planos_folios.m2) as total_m2_calc'),
            DB::raw('GROUP_CONCAT(DISTINCT planos_folios.solicitante ORDER BY planos_folios.id SEPARATOR ", ") as solicitantes'),
            DB::raw('GROUP_CONCAT(DISTINCT planos_folios.apellido_paterno ORDER BY planos_folios.id SEPARATOR ", ") as apellidos_paternos'),
            DB::raw('GROUP_CONCAT(DISTINCT planos_folios.apellido_materno ORDER BY planos_folios.id SEPARATOR ", ") as apellidos_maternos'),
        ])
        ->leftJoin('planos_folios', 'planos.id', '=', 'planos_folios.plano_id')
        ->with(['folios' => function($query) {
            $query->orderBy('id')->take(2);
        }])
        ->groupBy('planos.id');

        // Filtros
        if ($request->filled('comuna')) {
            $query->where('planos.comuna', $request->comuna);
        }

        if ($request->filled('ano')) {
            $query->whereYear('planos.created_at', $request->ano);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('planos.created_at', $request->mes);
        }

        if ($request->filled('responsable')) {
            $query->where('planos.responsable', $request->responsable);
        }

        if ($request->filled('proyecto')) {
            $query->where('planos.proyecto', $request->proyecto);
        }

        if ($request->filled('folio')) {
            $query->whereHas('folios', function($q) use ($request) {
                $q->where('folio', 'LIKE', '%' . $request->folio . '%');
            });
        }

        return DataTables::of($query)
            ->addColumn('acciones', function ($plano) {
                $acciones = '';
                if (Auth::user()->isRegistro()) {
                    $acciones .= '<button class="btn btn-sm btn-primary editar-plano" data-id="'.$plano->id.'"><i class="fas fa-edit"></i></button> ';
                    $acciones .= '<button class="btn btn-sm btn-warning reasignar-plano" data-id="'.$plano->id.'"><i class="fas fa-exchange-alt"></i></button>';
                }
                return $acciones;
            })
            ->addColumn('folios_display', function ($plano) {
                return $plano->display_folios;
            })
            ->addColumn('solicitante_display', function ($plano) {
                $folios = $plano->folios;
                if ($folios->isEmpty()) return '-';

                $solicitantes = $folios->pluck('solicitante')->unique()->filter();
                if ($solicitantes->count() > 1) {
                    return 'MÚLTIPLES';
                }
                return $solicitantes->first() ?: '-';
            })
            ->addColumn('apellido_paterno_display', function ($plano) {
                $folios = $plano->folios;
                if ($folios->isEmpty()) return '-';

                $apellidos = $folios->pluck('apellido_paterno')->unique()->filter();
                if ($apellidos->count() > 1) {
                    return '-';
                }
                return $apellidos->first() ?: '-';
            })
            ->addColumn('apellido_materno_display', function ($plano) {
                $folios = $plano->folios;
                if ($folios->isEmpty()) return '-';

                $apellidos = $folios->pluck('apellido_materno')->unique()->filter();
                if ($apellidos->count() > 1) {
                    return '-';
                }
                return $apellidos->first() ?: '-';
            })
            ->addColumn('hectareas_display', function ($plano) {
                return $plano->total_hectareas ? number_format($plano->total_hectareas, 2) : '-';
            })
            ->addColumn('m2_display', function ($plano) {
                return number_format($plano->total_m2 ?: 0);
            })
            ->addColumn('mes_display', function ($plano) {
                return $plano->mes;
            })
            ->addColumn('ano_display', function ($plano) {
                return $plano->ano;
            })
            ->addColumn('expandir', function ($plano) {
                return '<button class="btn btn-sm btn-info expandir-folios" data-id="'.$plano->id.'"><i class="fas fa-plus"></i></button>';
            })
            ->rawColumns(['acciones', 'expandir'])
            ->make(true);
    }

    public function getFoliosExpansion($planoId)
    {
        $plano = Plano::with('folios')->findOrFail($planoId);

        $html = '';
        foreach ($plano->folios as $folio) {
            $html .= '<tr class="child-row bg-light">';
            $html .= '<td></td><td></td>'; // Columnas vacías para acciones
            $html .= '<td class="pl-4">└ Folio</td>';
            $html .= '<td>' . $folio->folio . '</td>';
            $html .= '<td>' . ($folio->solicitante ?: '-') . '</td>';
            $html .= '<td>' . ($folio->apellido_paterno ?: '-') . '</td>';
            $html .= '<td>' . ($folio->apellido_materno ?: '-') . '</td>';
            $html .= '<td></td>'; // Comuna vacía en detalle
            $html .= '<td>' . ($folio->hectareas ? number_format($folio->hectareas, 2) : '-') . '</td>';
            $html .= '<td>' . number_format($folio->m2 ?: 0) . '</td>';
            $html .= '<td colspan="5"></td>'; // Resto vacío
            $html .= '</tr>';
        }

        return response()->json(['html' => $html]);
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