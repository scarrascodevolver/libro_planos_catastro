<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatrixImport;
use App\Models\Plano;
use App\Models\PlanoFolio;
use App\Models\ComunaBiobio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class PlanoImportacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isRegistro()) {
                abort(403, 'Solo usuarios con rol "registro" pueden importar datos');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $ultimoBatch = MatrixImport::getUltimoBatch();
        $totalMatrix = MatrixImport::count();

        return view('admin.planos.importacion', compact('ultimoBatch', 'totalMatrix'));
    }

    public function previewMatrix(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:10240' // 10MB max
        ]);

        try {
            $archivo = $request->file('archivo');
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            $headers = [];
            $preview = [];
            $errores = [];

            // Leer headers (fila 1)
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1';
                $headers[] = $worksheet->getCell($coordinate)->getValue();
            }

            // Validar headers requeridos para Matrix
            $requiredHeaders = [
                'TIPO INMUEBLE', 'Comuna', 'NOMBRES', 'APELLIDO PATERNO',
                'APELLIDO MATERNO', 'FOLIOS-EXPEDIENTES', 'RESPONSABLE', 'CONVENIO-FINANCIAMIENTO'
            ];

            $headersEncontrados = [];
            foreach ($requiredHeaders as $required) {
                $encontrado = false;
                foreach ($headers as $header) {
                    if (stripos($header, $required) !== false) {
                        $headersEncontrados[$required] = $header;
                        $encontrado = true;
                        break;
                    }
                }
                if (!$encontrado) {
                    $errores[] = "Header requerido no encontrado: {$required}";
                }
            }

            if (!empty($errores)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo Matrix inválido',
                    'errores' => $errores
                ]);
            }

            // Mapear columnas para validación
            $columnMap = $this->mapMatrixColumns($headers);
            if (!$columnMap) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudieron mapear las columnas requeridas'
                ]);
            }

            // Leer primeras 10 filas para preview visual
            $highestRow = $worksheet->getHighestRow();
            $maxPreview = min(11, $highestRow); // Fila 1 = headers, filas 2-11 = datos

            for ($row = 2; $row <= $maxPreview; $row++) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                    $rowData[] = $worksheet->getCell($coordinate)->getValue();
                }
                $preview[] = $rowData;
            }

            $totalFilas = $highestRow - 1; // -1 porque no contamos headers

            // Validar TODOS los campos obligatorios en TODAS las filas
            $camposObligatorios = [
                'folio' => 'Folio',
                'tipo_inmueble' => 'Tipo Inmueble',
                'comuna' => 'Comuna',
                'nombres' => 'Nombres',
                'apellido_paterno' => 'Apellido Paterno',
                'apellido_materno' => 'Apellido Materno',
                'responsable' => 'Responsable',
                'convenio_financiamiento' => 'Convenio/Financiamiento'
            ];

            $erroresPorCampo = array_fill_keys(array_keys($camposObligatorios), 0);
            $detalleErrores = [];
            $registrosValidos = 0;

            $filasVaciasIgnoradas = 0;

            for ($row = 2; $row <= $highestRow; $row++) {
                // Primero verificar si la fila está completamente vacía
                $filaVacia = true;
                $valoresFila = [];

                foreach ($columnMap as $campo => $colIndex) {
                    $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $row;
                    $valor = trim($worksheet->getCell($coordinate)->getValue() ?? '');
                    $valoresFila[$campo] = $valor;

                    if (!empty($valor)) {
                        $filaVacia = false;
                    }
                }

                // Si la fila está completamente vacía, ignorarla
                if ($filaVacia) {
                    $filasVaciasIgnoradas++;
                    continue;
                }

                // Validar campos vacíos en filas con datos
                $camposVacios = [];
                $folio = $valoresFila['folio'] ?? '';

                foreach ($valoresFila as $campo => $valor) {
                    if (empty($valor)) {
                        $camposVacios[] = $camposObligatorios[$campo];
                        $erroresPorCampo[$campo]++;
                    }
                }

                if (empty($camposVacios)) {
                    $registrosValidos++;
                } else {
                    $detalleErrores[] = [
                        'fila' => $row,
                        'folio' => $folio ?: '(vacío)',
                        'campos' => $camposVacios
                    ];
                }
            }

            // Ajustar total de filas reales (sin filas vacías)
            $totalFilasReales = $totalFilas - $filasVaciasIgnoradas;

            $registrosConErrores = count($detalleErrores);

            // Limpiar contadores en cero
            $erroresPorCampo = array_filter($erroresPorCampo);

            return response()->json([
                'success' => true,
                'headers' => $headers,
                'headersEncontrados' => $headersEncontrados,
                'preview' => $preview,
                'totalFilas' => $totalFilasReales,
                'filasVaciasIgnoradas' => $filasVaciasIgnoradas,
                'registrosValidos' => $registrosValidos,
                'registrosConErrores' => $registrosConErrores,
                'erroresPorCampo' => $erroresPorCampo,
                'detalleErrores' => $detalleErrores,
                'mensaje' => $registrosConErrores > 0
                    ? "Archivo con {$totalFilasReales} registros. {$registrosConErrores} tienen campos vacíos."
                    : "Archivo válido. {$totalFilasReales} registros listos para importar."
            ]);

        } catch (ReaderException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al leer archivo: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage()
            ]);
        }
    }

    public function importMatrix(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:10240',
            'batch_name' => 'required|string|max:50',
            'accion_duplicados' => 'required|in:actualizar,mantener'
        ]);

        try {
            $archivo = $request->file('archivo');
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            $batchName = $request->batch_name;
            $actualizarDuplicados = $request->accion_duplicados === 'actualizar';

            // Obtener headers
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            $headers = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1';
                $headers[] = $worksheet->getCell($coordinate)->getValue();
            }

            // Mapear columnas
            $columnMap = $this->mapMatrixColumns($headers);
            if (!$columnMap) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudieron mapear las columnas requeridas'
                ]);
            }

            $highestRow = $worksheet->getHighestRow();
            $procesados = 0;
            $actualizados = 0;
            $nuevos = 0;
            $errores = [];

            DB::beginTransaction();

            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $datos = [];
                    foreach ($columnMap as $campo => $colIndex) {
                        $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $row;
                        $datos[$campo] = $worksheet->getCell($coordinate)->getValue();
                    }

                    // Limpiar y validar datos
                    $datos = $this->cleanMatrixData($datos);

                    if (empty($datos['folio'])) {
                        $errores[] = "Fila {$row}: Folio vacío, omitida";
                        continue;
                    }

                    // Verificar si existe
                    $existente = MatrixImport::where('folio', $datos['folio'])->first();

                    if ($existente && !$actualizarDuplicados) {
                        continue; // Mantener existente
                    }

                    $datos['batch_import'] = $batchName;

                    if ($existente && $actualizarDuplicados) {
                        $existente->update($datos);
                        $actualizados++;
                    } else {
                        MatrixImport::create($datos);
                        $nuevos++;
                    }

                    $procesados++;

                } catch (\Exception $e) {
                    $errores[] = "Fila {$row}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Importación Matrix completada',
                'estadisticas' => [
                    'procesados' => $procesados,
                    'nuevos' => $nuevos,
                    'actualizados' => $actualizados,
                    'errores' => count($errores)
                ],
                'errores' => $errores,
                'batchName' => $batchName
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Error en importación: ' . $e->getMessage()
            ]);
        }
    }

    public function previewHistoricos(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:20480' // 20MB max para históricos
        ]);

        try {
            $archivo = $request->file('archivo');
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            $headers = [];
            $preview = [];

            // Leer headers
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1';
                $headers[] = $worksheet->getCell($coordinate)->getValue();
            }

            // Preview de 5 filas
            $highestRow = $worksheet->getHighestRow();
            $maxPreview = min(6, $highestRow);

            for ($row = 2; $row <= $maxPreview; $row++) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $coordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;
                    $rowData[] = $worksheet->getCell($coordinate)->getValue();
                }
                $preview[] = $rowData;
            }

            $totalFilas = $highestRow - 1;

            return response()->json([
                'success' => true,
                'headers' => $headers,
                'preview' => $preview,
                'totalFilas' => $totalFilas,
                'mensaje' => "Archivo históricos detectado. {$totalFilas} registros para procesar."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al leer archivo: ' . $e->getMessage()
            ]);
        }
    }

    public function importHistoricos(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:20480'
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Importación de históricos no implementada aún. Requiere mapeo específico de las 21 columnas.'
        ]);
    }

    private function mapMatrixColumns(array $headers): ?array
    {
        $map = [];
        $required = [
            'tipo_inmueble' => ['TIPO INMUEBLE', 'TIPO_INMUEBLE'],
            'comuna' => ['Comuna', 'COMUNA'],
            'nombres' => ['NOMBRES', 'NOMBRE'],
            'apellido_paterno' => ['APELLIDO PATERNO', 'APELLIDO_PATERNO', 'AP PATERNO'],
            'apellido_materno' => ['APELLIDO MATERNO', 'APELLIDO_MATERNO', 'AP MATERNO'],
            'folio' => ['FOLIOS-EXPEDIENTES', 'FOLIO', 'FOLIOS'],
            'responsable' => ['RESPONSABLE'],
            'convenio_financiamiento' => ['CONVENIO-FINANCIAMIENTO', 'CONVENIO', 'FINANCIAMIENTO']
        ];

        foreach ($required as $campo => $posibles) {
            $encontrado = false;
            foreach ($headers as $index => $header) {
                foreach ($posibles as $posible) {
                    if (stripos($header, $posible) !== false) {
                        $map[$campo] = $index + 1; // +1 porque PhpSpreadsheet usa base 1
                        $encontrado = true;
                        break 2;
                    }
                }
            }
            if (!$encontrado) {
                return null; // Campo requerido no encontrado
            }
        }

        return $map;
    }

    private function cleanMatrixData(array $datos): array
    {
        return [
            'folio' => trim($datos['folio'] ?? ''),
            'tipo_inmueble' => trim($datos['tipo_inmueble'] ?? ''),
            'provincia' => 'Biobío', // Asumimos siempre Biobío
            'comuna' => trim($datos['comuna'] ?? ''),
            'nombres' => trim($datos['nombres'] ?? ''),
            'apellido_paterno' => trim($datos['apellido_paterno'] ?? ''),
            'apellido_materno' => trim($datos['apellido_materno'] ?? ''),
            'responsable' => trim($datos['responsable'] ?? ''),
            'convenio_financiamiento' => trim($datos['convenio_financiamiento'] ?? ''),
        ];
    }

    public function getEstadisticasMatrix()
    {
        $total = MatrixImport::count();
        $ultimoBatch = MatrixImport::getUltimoBatch();
        $foliosUnicos = MatrixImport::distinct('folio')->count();
        $comunas = MatrixImport::select('comuna')->distinct()->count();

        return response()->json([
            'total' => $total,
            'ultimoBatch' => $ultimoBatch,
            'foliosUnicos' => $foliosUnicos,
            'comunas' => $comunas
        ]);
    }

    public function getEstadisticasHistoricos()
    {
        // Contar TODOS los planos, no solo históricos
        $totalPlanos = Plano::count();
        $totalFolios = PlanoFolio::count();

        return response()->json([
            'total_planos' => $totalPlanos,
            'total_folios' => $totalFolios
        ]);
    }

    public function limpiarMatrix(Request $request)
    {
        $request->validate([
            'batch' => 'required|string'
        ]);

        $eliminados = MatrixImport::where('batch_import', $request->batch)->delete();

        return response()->json([
            'success' => true,
            'message' => "Batch '{$request->batch}' eliminado: {$eliminados} registros"
        ]);
    }

    public function buscarFolioMatrix(Request $request)
    {
        $folio = $request->get('folio');

        if (empty($folio)) {
            return response()->json([
                'success' => false,
                'message' => 'Folio requerido'
            ]);
        }

        $registro = MatrixImport::where('folio', $folio)->first();

        if ($registro) {
            return response()->json([
                'success' => true,
                'data' => [
                    'folio' => $registro->folio,
                    'nombres' => $registro->nombres,
                    'apellido_paterno' => $registro->apellido_paterno,
                    'apellido_materno' => $registro->apellido_materno,
                    'tipo_inmueble' => $registro->tipo_inmueble,
                    'comuna' => $registro->comuna,
                    'responsable' => $registro->responsable,
                    'convenio_financiamiento' => $registro->convenio_financiamiento
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Folio no encontrado en Matrix'
        ]);
    }

    /**
     * Eliminar TODOS los datos de Matrix importados
     * SOLO usuarios con rol 'registro'
     */
    public function eliminarTodosMatrix()
    {
        try {
            $totalRegistros = MatrixImport::count();

            if ($totalRegistros === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay registros Matrix para eliminar'
                ]);
            }

            // Log crítico antes de eliminar
            \Log::warning('ELIMINACIÓN MASIVA MATRIX', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'total_registros' => $totalRegistros,
                'timestamp' => now()
            ]);

            // Eliminar todos los registros
            MatrixImport::truncate();

            // Log de confirmación
            \Log::warning('ELIMINACIÓN MASIVA MATRIX COMPLETADA', [
                'user_id' => Auth::id(),
                'registros_eliminados' => $totalRegistros
            ]);

            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$totalRegistros} registros de Matrix exitosamente",
                'registros_eliminados' => $totalRegistros
            ]);

        } catch (\Exception $e) {
            \Log::error('ERROR ELIMINACIÓN MATRIX', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar registros Matrix: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar TODOS los planos
     * Confirmación requerida en frontend
     */
    public function eliminarHistoricos(Request $request)
    {
        try {
            // VALIDAR CONTROL DE SESIÓN - Usuario debe tener control activo
            $tieneControl = DB::table('session_control')
                ->where('user_id', Auth::id())
                ->where('has_control', true)
                ->where('is_active', true)
                ->exists();

            if (!$tieneControl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debes tener el control de numeración activo para eliminar planos'
                ], 403);
            }

            // Validar texto de confirmación
            $request->validate([
                'confirmacion' => 'required|string'
            ]);

            if ($request->confirmacion !== 'BORRAR PLANOS') {
                return response()->json([
                    'success' => false,
                    'message' => 'Texto de confirmación incorrecto. Debe escribir exactamente: BORRAR PLANOS'
                ], 422);
            }

            // Contar TODOS los planos
            $totalPlanos = Plano::count();

            if ($totalPlanos === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay planos para eliminar'
                ]);
            }

            // Contar TODOS los folios (se eliminarán por CASCADE)
            $totalFolios = PlanoFolio::count();

            DB::beginTransaction();

            try {
                // Log crítico antes de eliminar
                \Log::critical('ELIMINACIÓN MASIVA TODOS LOS PLANOS', [
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email,
                    'total_planos' => $totalPlanos,
                    'total_folios' => $totalFolios,
                    'timestamp' => now(),
                    'confirmacion' => $request->confirmacion
                ]);

                // Eliminar TODOS los planos (folios se eliminan por CASCADE)
                Plano::truncate();

                DB::commit();

                // Log de confirmación
                \Log::critical('ELIMINACIÓN MASIVA PLANOS COMPLETADA', [
                    'user_id' => Auth::id(),
                    'planos_eliminados' => $totalPlanos,
                    'folios_eliminados' => $totalFolios
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Se eliminaron {$totalPlanos} planos históricos con {$totalFolios} folios asociados",
                    'planos_eliminados' => $totalPlanos,
                    'folios_eliminados' => $totalFolios
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('ERROR ELIMINACIÓN HISTÓRICOS', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar planos históricos: ' . $e->getMessage()
            ], 500);
        }
    }
}