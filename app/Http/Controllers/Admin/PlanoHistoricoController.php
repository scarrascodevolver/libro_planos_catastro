<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\PlanoFolio;
use App\Models\ComunaBiobio;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PlanoHistoricoController extends Controller
{
    public function showImportForm()
    {
        return view('admin.planos.historico');
    }

    public function previewExcel(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $archivo = $request->file('archivo_excel');
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $datos = $worksheet->toArray();

            // Remover header si existe
            if (count($datos) > 0 && $this->isHeader($datos[0])) {
                array_shift($datos);
            }

            // Agrupar datos por clave única
            $grupos = $this->agruparDatos($datos);

            // Validar datos agrupados
            $validacion = $this->validarGrupos($grupos);

            return response()->json([
                'success' => true,
                'total_filas' => count($datos),
                'total_grupos' => count($grupos),
                'grupos_validos' => $validacion['validos'],
                'grupos_invalidos' => $validacion['invalidos'],
                'errores' => $validacion['errores'],
                'preview' => array_slice($grupos, 0, 5) // Primeros 5 grupos para preview
            ]);

        } catch (\Exception $e) {
            Log::error('Error en preview Excel histórico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importHistorico(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            DB::beginTransaction();

            $archivo = $request->file('archivo_excel');
            $spreadsheet = IOFactory::load($archivo->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $datos = $worksheet->toArray();

            // Remover header si existe
            if (count($datos) > 0 && $this->isHeader($datos[0])) {
                array_shift($datos);
            }

            // Agrupar y procesar datos
            $grupos = $this->agruparDatos($datos);
            $resultado = $this->procesarGrupos($grupos);

            if ($resultado['errores_criticos'] > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Importación cancelada por errores críticos',
                    'resultado' => $resultado
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Importación completada exitosamente',
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en importación histórica: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en la importación: ' . $e->getMessage()
            ], 500);
        }
    }

    private function isHeader($fila)
    {
        // Verificar si la primera fila contiene headers
        return in_array('CODIGO_REGIONAL', $fila) ||
               in_array('N°_PLANO', $fila) ||
               in_array('FOLIO', $fila);
    }

    private function agruparDatos($datos)
    {
        $grupos = [];

        foreach ($datos as $index => $fila) {
            // Saltear filas vacías
            if (empty(array_filter($fila))) continue;

            // Mapear índices de columnas (según estructura confirmada)
            $registro = $this->mapearFilaExcel($fila, $index);

            // Crear clave de agrupación
            $claveGrupo = $registro['CODIGO_REGIONAL'] . '_' .
                         $registro['CODIGO_COMUNAL'] . '_' .
                         $registro['N°_PLANO'] . '_' .
                         $registro['URBANO/RURAL'];

            if (!isset($grupos[$claveGrupo])) {
                $grupos[$claveGrupo] = [];
            }

            $grupos[$claveGrupo][] = $registro;
        }

        return $grupos;
    }

    private function mapearFilaExcel($fila, $numeroFila)
    {
        // Mapeo según estructura Excel confirmada (24 columnas)
        return [
            'NUMERO_FILA' => $numeroFila + 1,
            'CODIGO_REGIONAL' => $fila[0] ?? '',
            'CODIGO_COMUNAL' => $fila[1] ?? '',
            'N°_PLANO' => $fila[2] ?? '',
            'URBANO/RURAL' => $fila[3] ?? '',
            'FOLIO' => $fila[4] ?? '',
            'SOLICITANTE' => $fila[5] ?? '',
            'PATERNO' => $fila[6] ?? '',
            'MATERNO' => $fila[7] ?? '',
            'COMUNA' => $fila[8] ?? '',
            'HIJ' => intval($fila[9] ?? 0),
            'HA' => floatval($fila[10] ?? 0),
            'M²_HIJ' => intval($fila[11] ?? 0), // Primera columna M²
            'SITIO' => intval($fila[12] ?? 0),
            'M²_SITIO' => intval($fila[13] ?? 0), // Segunda columna M²
            'FECHA' => $fila[14] ?? '',
            'AÑO' => intval($fila[15] ?? 0),
            'Responsable' => $fila[16] ?? '',
            'PROYECTO' => $fila[17] ?? '',
            'PROVIDENCIA' => $fila[18] ?? '',
            'ARCHIVO' => $fila[19] ?? '',
            'OBSERVACION' => $fila[20] ?? '',
            'TUBO' => $fila[21] ?? '',
            'TELA' => $fila[22] ?? '',
            'ARCHIVO_DIGITAL' => $fila[23] ?? ''
        ];
    }

    private function validarGrupos($grupos)
    {
        $validos = 0;
        $invalidos = 0;
        $errores = [];

        foreach ($grupos as $clave => $grupo) {
            $erroresGrupo = $this->validarGrupo($grupo);

            if (empty($erroresGrupo)) {
                $validos++;
            } else {
                $invalidos++;
                $errores[$clave] = $erroresGrupo;
            }
        }

        return [
            'validos' => $validos,
            'invalidos' => $invalidos,
            'errores' => $errores
        ];
    }

    private function validarGrupo($grupo)
    {
        $errores = [];
        $primerFila = $grupo[0];

        // Validar número plano único
        if (Plano::where('numero_plano', $primerFila['N°_PLANO'])->exists()) {
            $errores[] = "Número de plano {$primerFila['N°_PLANO']} ya existe";
        }

        // Validar comuna existe
        $comuna = ComunaBiobio::where('nombre', $primerFila['COMUNA'])->first();
        if (!$comuna) {
            $errores[] = "Comuna '{$primerFila['COMUNA']}' no encontrada";
        }

        // Validar cada folio del grupo
        foreach ($grupo as $fila) {
            $erroresFila = $this->validarFila($fila);
            if (!empty($erroresFila)) {
                $errores = array_merge($errores, $erroresFila);
            }
        }

        return $errores;
    }

    private function validarFila($fila)
    {
        $errores = [];

        // Validar que tenga HIJ o SITIO
        if ($fila['HIJ'] <= 0 && $fila['SITIO'] <= 0) {
            $errores[] = "Fila {$fila['NUMERO_FILA']}: Debe tener HIJ o SITIO > 0";
        }

        // Validar M² coherente
        $m2 = ($fila['HIJ'] > 0) ? $fila['M²_HIJ'] : $fila['M²_SITIO'];
        if ($m2 <= 0) {
            $errores[] = "Fila {$fila['NUMERO_FILA']}: M² debe ser > 0";
        }

        // Validar campos obligatorios
        if (empty($fila['FOLIO'])) {
            $errores[] = "Fila {$fila['NUMERO_FILA']}: FOLIO requerido";
        }

        if (empty($fila['SOLICITANTE'])) {
            $errores[] = "Fila {$fila['NUMERO_FILA']}: SOLICITANTE requerido";
        }

        return $errores;
    }

    private function procesarGrupos($grupos)
    {
        $planosCreados = 0;
        $foliosCreados = 0;
        $errores = [];
        $erroresCriticos = 0;

        foreach ($grupos as $clave => $grupo) {
            try {
                // Validar grupo antes de procesar
                $erroresGrupo = $this->validarGrupo($grupo);
                if (!empty($erroresGrupo)) {
                    $errores[$clave] = $erroresGrupo;
                    $erroresCriticos++;
                    continue;
                }

                // Crear plano
                $plano = $this->crearPlanoDesdeGrupo($grupo);
                $planosCreados++;

                // Crear folios
                foreach ($grupo as $fila) {
                    $this->crearFolioDesdeFila($plano->id, $fila);
                    $foliosCreados++;
                }

            } catch (\Exception $e) {
                $errores[$clave] = ["Error al procesar: " . $e->getMessage()];
                $erroresCriticos++;
                Log::error("Error procesando grupo $clave: " . $e->getMessage());
            }
        }

        return [
            'planos_creados' => $planosCreados,
            'folios_creados' => $foliosCreados,
            'errores' => $errores,
            'errores_criticos' => $erroresCriticos
        ];
    }

    private function crearPlanoDesdeGrupo($grupo)
    {
        $primerFila = $grupo[0];

        // Calcular totales del grupo
        $totalHectareas = collect($grupo)->sum('HA');
        $totalM2 = collect($grupo)->sum(function($fila) {
            return ($fila['HIJ'] > 0) ? $fila['M²_HIJ'] : $fila['M²_SITIO'];
        });

        // Lookup provincia
        $comuna = ComunaBiobio::where('nombre', $primerFila['COMUNA'])->first();
        $provincia = $comuna ? $comuna->provincia : 'DESCONOCIDA';

        // Extraer mes de fecha
        $mes = $this->extraerMes($primerFila['FECHA']);

        return Plano::create([
            'numero_plano' => $primerFila['N°_PLANO'],
            'codigo_region' => $primerFila['CODIGO_REGIONAL'],
            'codigo_comuna' => $primerFila['CODIGO_COMUNAL'],
            'numero_correlativo' => $primerFila['N°_PLANO'], // Por ahora el mismo valor
            'tipo_saneamiento' => $primerFila['URBANO/RURAL'],
            'provincia' => $provincia,
            'comuna' => $primerFila['COMUNA'],
            'mes' => $mes,
            'ano' => $primerFila['AÑO'],
            'responsable' => $primerFila['Responsable'],
            'proyecto' => $primerFila['PROYECTO'],
            'providencia' => $primerFila['PROVIDENCIA'],
            'total_hectareas' => $totalHectareas > 0 ? $totalHectareas : null,
            'total_m2' => $totalM2,
            'cantidad_folios' => count($grupo),
            'observaciones' => $primerFila['OBSERVACION'],
            'archivo' => $primerFila['ARCHIVO'],
            'tubo' => $primerFila['TUBO'],
            'tela' => $primerFila['TELA'],
            'archivo_digital' => $primerFila['ARCHIVO_DIGITAL'],
            'created_by' => auth()->id()
        ]);
    }

    private function crearFolioDesdeFila($planoId, $fila)
    {
        // Determinar tipo de inmueble y valores
        if ($fila['HIJ'] > 0) {
            $tipoInmueble = 'HIJUELA';
            $numeroInmueble = $fila['HIJ'];
            $hectareas = $fila['HA'];
            $m2 = $fila['M²_HIJ'];
        } else {
            $tipoInmueble = 'SITIO';
            $numeroInmueble = $fila['SITIO'];
            $hectareas = null;
            $m2 = $fila['M²_SITIO'];
        }

        return PlanoFolio::create([
            'plano_id' => $planoId,
            'folio' => $fila['FOLIO'],
            'solicitante' => $fila['SOLICITANTE'],
            'apellido_paterno' => $fila['PATERNO'] ?: null,
            'apellido_materno' => $fila['MATERNO'] ?: null,
            'tipo_inmueble' => $tipoInmueble,
            'numero_inmueble' => $numeroInmueble,
            'hectareas' => $hectareas,
            'm2' => $m2,
            'is_from_matrix' => false,
            'matrix_folio' => null
        ]);
    }

    private function extraerMes($fecha)
    {
        if (empty($fecha)) return 'DESCONOCIDO';

        try {
            $carbon = Carbon::parse($fecha);
            return strtoupper($carbon->format('M'));
        } catch (\Exception $e) {
            return 'DESCONOCIDO';
        }
    }
}
