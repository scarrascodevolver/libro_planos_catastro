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

            // Leer primeras 10 filas para preview
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

            return response()->json([
                'success' => true,
                'headers' => $headers,
                'headersEncontrados' => $headersEncontrados,
                'preview' => $preview,
                'totalFilas' => $totalFilas,
                'mensaje' => "Archivo válido. {$totalFilas} registros listos para importar."
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
}