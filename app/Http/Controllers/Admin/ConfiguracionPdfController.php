<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConfiguracionPdfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Solo usuarios con rol 'registro' pueden acceder
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isRegistro()) {
                abort(403, 'No tienes permisos para acceder a esta sección');
            }
            return $next($request);
        });
    }

    /**
     * Mostrar listado de configuraciones
     */
    public function index()
    {
        $configuraciones = ConfiguracionPdf::orderBy('ano', 'desc')->get();
        return view('admin.configuracion-pdf.index', compact('configuraciones'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.configuracion-pdf.create');
    }

    /**
     * Guardar nueva configuración
     */
    public function store(Request $request)
    {
        // DEBUG: Log para ver si llega la petición
        Log::info('STORE CONFIGURACION PDF - Petición recibida', [
            'datos' => $request->all(),
            'usuario' => Auth::user()->name
        ]);

        $request->validate([
            'ano' => 'required|integer|min:2000|max:2100|unique:configuracion_pdfs,ano',
            'ruta_base' => 'required|string|max:500'
        ], [
            'ano.required' => 'El año es obligatorio',
            'ano.integer' => 'El año debe ser un número válido',
            'ano.min' => 'El año debe ser mayor o igual a 2000',
            'ano.max' => 'El año debe ser menor o igual a 2100',
            'ano.unique' => 'Ya existe una configuración para este año',
            'ruta_base.required' => 'La ruta base es obligatoria',
            'ruta_base.max' => 'La ruta base no puede exceder 500 caracteres'
        ]);

        try {
            $configuracion = ConfiguracionPdf::create([
                'ano' => $request->ano,
                'ruta_base' => $request->ruta_base,
                'activo' => $request->has('activo') ? true : false
            ]);

            Log::info('CONFIGURACION PDF CREADA', [
                'id' => $configuracion->id,
                'ano' => $configuracion->ano,
                'ruta_base' => $configuracion->ruta_base,
                'usuario' => Auth::user()->name
            ]);

            return redirect()
                ->route('admin.configuracion-pdf.index')
                ->with('success', 'Configuración creada exitosamente');

        } catch (\Exception $e) {
            Log::error('ERROR AL CREAR CONFIGURACION PDF', [
                'error' => $e->getMessage(),
                'usuario' => Auth::user()->name
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al crear configuración: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $configuracion = ConfiguracionPdf::findOrFail($id);
        return view('admin.configuracion-pdf.edit', compact('configuracion'));
    }

    /**
     * Actualizar configuración existente
     */
    public function update(Request $request, $id)
    {
        $configuracion = ConfiguracionPdf::findOrFail($id);

        $request->validate([
            'ano' => 'required|integer|min:2000|max:2100|unique:configuracion_pdfs,ano,' . $id,
            'ruta_base' => 'required|string|max:500'
        ], [
            'ano.required' => 'El año es obligatorio',
            'ano.integer' => 'El año debe ser un número válido',
            'ano.min' => 'El año debe ser mayor o igual a 2000',
            'ano.max' => 'El año debe ser menor o igual a 2100',
            'ano.unique' => 'Ya existe otra configuración para este año',
            'ruta_base.required' => 'La ruta base es obligatoria',
            'ruta_base.max' => 'La ruta base no puede exceder 500 caracteres'
        ]);

        try {
            $configuracion->update([
                'ano' => $request->ano,
                'ruta_base' => $request->ruta_base,
                'activo' => $request->has('activo') ? true : false
            ]);

            Log::info('CONFIGURACION PDF ACTUALIZADA', [
                'id' => $configuracion->id,
                'ano' => $configuracion->ano,
                'ruta_base' => $configuracion->ruta_base,
                'activo' => $configuracion->activo,
                'usuario' => Auth::user()->name
            ]);

            return redirect()
                ->route('admin.configuracion-pdf.index')
                ->with('success', 'Configuración actualizada exitosamente');

        } catch (\Exception $e) {
            Log::error('ERROR AL ACTUALIZAR CONFIGURACION PDF', [
                'id' => $id,
                'error' => $e->getMessage(),
                'usuario' => Auth::user()->name
            ]);

            return back()
                ->withInput()
                ->with('error', 'Error al actualizar configuración: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar configuración
     */
    public function destroy($id)
    {
        try {
            $configuracion = ConfiguracionPdf::findOrFail($id);

            Log::info('CONFIGURACION PDF ELIMINADA', [
                'id' => $configuracion->id,
                'ano' => $configuracion->ano,
                'usuario' => Auth::user()->name
            ]);

            $configuracion->delete();

            return redirect()
                ->route('admin.configuracion-pdf.index')
                ->with('success', 'Configuración eliminada exitosamente');

        } catch (\Exception $e) {
            Log::error('ERROR AL ELIMINAR CONFIGURACION PDF', [
                'id' => $id,
                'error' => $e->getMessage(),
                'usuario' => Auth::user()->name
            ]);

            return back()
                ->with('error', 'Error al eliminar configuración: ' . $e->getMessage());
        }
    }

    /**
     * Verificar si una ruta existe y es accesible
     */
    public function verificarRuta(Request $request)
    {
        $ruta = $request->input('ruta');

        if (empty($ruta)) {
            return response()->json([
                'existe' => false,
                'mensaje' => 'Ruta vacía'
            ]);
        }

        $existe = file_exists($ruta) && is_dir($ruta);
        $esLegible = $existe && is_readable($ruta);

        return response()->json([
            'existe' => $existe,
            'legible' => $esLegible,
            'mensaje' => $existe
                ? ($esLegible ? 'Ruta válida y accesible' : 'Ruta existe pero no es legible')
                : 'Ruta no encontrada o no es un directorio'
        ]);
    }
}
