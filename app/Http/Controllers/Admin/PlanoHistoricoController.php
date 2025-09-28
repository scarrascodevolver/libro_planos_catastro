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

            // Crear headers para la vista previa (24 columnas)
            $headers = [
                'CODIGO_REGIONAL', 'CODIGO_COMUNAL', 'N°_PLANO', 'URBANO/RURAL',
                'FOLIO', 'SOLICITANTE', 'PATERNO', 'MATERNO', 'COMUNA',
                'HIJ', 'HA', 'M²_HIJ', 'SITIO', 'M²_SITIO', 'FECHA', 'AÑO',
                'Responsable', 'PROYECTO', 'PROVIDENCIA', 'ARCHIVO',
                'OBSERVACION', 'TUBO', 'TELA', 'ARCHIVO_DIGITAL'
            ];

            // Agrupar datos por clave única
            $grupos = $this->agruparDatos($datos);

            // Validar datos agrupados
            $validacion = $this->validarGrupos($grupos);

            // Convertir primeros grupos a formato para preview (filas individuales)
            $previewFilas = [];
            $gruposParaPreview = array_slice($grupos, 0, 3); // Primeros 3 grupos
            foreach ($gruposParaPreview as $grupo) {
                foreach ($grupo as $fila) {
                    $previewFilas[] = [
                        $fila['CODIGO_REGIONAL'], $fila['CODIGO_COMUNAL'], $fila['N°_PLANO'], $fila['URBANO/RURAL'],
                        $fila['FOLIO'], $fila['SOLICITANTE'], $fila['PATERNO'], $fila['MATERNO'], $fila['COMUNA'],
                        $fila['HIJ'], $fila['HA'], $fila['M²_HIJ'], $fila['SITIO'], $fila['M²_SITIO'], $fila['FECHA'], $fila['AÑO'],
                        $fila['Responsable'], $fila['PROYECTO'], $fila['PROVIDENCIA'], $fila['ARCHIVO'],
                        $fila['OBSERVACION'], $fila['TUBO'], $fila['TELA'], $fila['ARCHIVO_DIGITAL']
                    ];
                }
                if (count($previewFilas) >= 10) break; // Máximo 10 filas para preview
            }

            return response()->json([
                'success' => true,
                'mensaje' => "Archivo procesado: {$validacion['validos']} grupos válidos de " . count($grupos) . " total",
                'headers' => $headers,
                'preview' => $previewFilas,
                'total_filas' => count($datos),
                'total_grupos' => count($grupos),
                'grupos_validos' => $validacion['validos'],
                'grupos_invalidos' => $validacion['invalidos'],
                'errores' => $validacion['errores']
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
            'CODIGO_COMUNAL' => $this->normalizarCodigoComuna($fila[1] ?? ''),
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
        $numerosProcessados = []; // Para validación de preview

        foreach ($grupos as $clave => $grupo) {
            $erroresGrupo = $this->validarGrupo($grupo, $numerosProcessados);

            // Si es válido, marcar número como procesado para siguientes validaciones
            if (empty($erroresGrupo)) {
                $numerosProcessados[] = $grupo[0]['N°_PLANO'];
            }

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

    private function validarGrupo($grupo, &$numerosProcessados = [])
    {
        $errores = [];
        $primerFila = $grupo[0];
        $numeroPlano = $primerFila['N°_PLANO'];

        // Validar número plano único en BD
        $existeEnBD = Plano::where('numero_plano', $numeroPlano)->exists();
        Log::info("Validando plano {$numeroPlano}: existe en BD = " . ($existeEnBD ? 'SÍ' : 'NO'));
        if ($existeEnBD) {
            $errores[] = "Número de plano {$numeroPlano} ya existe en BD";
        }

        // Validar número plano único en este lote de importación
        if (in_array($numeroPlano, $numerosProcessados)) {
            $errores[] = "Número de plano {$numeroPlano} duplicado en este archivo";
        }

        // Validar comuna existe (búsqueda inteligente)
        $nombreComuna = trim($primerFila['COMUNA']);
        $codigoComuna = $primerFila['CODIGO_COMUNAL']; // Código ya normalizado
        $comuna = $this->buscarComuna($nombreComuna, $codigoComuna);
        if (!$comuna) {
            $errores[] = "CRÍTICO: Comuna '{$nombreComuna}' no encontrada en BD";
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

        // Validar tipo de inmueble y datos coherentes
        $tipoResult = $this->determinarTipoInmueble($fila);
        if (isset($tipoResult['error'])) {
            $errores[] = "Fila {$fila['NUMERO_FILA']}: {$tipoResult['error']}";
        }

        // Validar FOLIO según tipo de plano (fiscal vs saneamiento)
        $errorFolio = $this->validarFolioSegunTipo($fila);
        if ($errorFolio) {
            $errores[] = $errorFolio;
        }

        // Validar SOLICITANTE (requerido pero puede ser genérico)
        if (empty(trim($fila['SOLICITANTE']))) {
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
        $numerosProcessados = []; // Track números ya procesados en este lote

        foreach ($grupos as $clave => $grupo) {
            try {
                // Validar grupo antes de procesar
                $erroresGrupo = $this->validarGrupo($grupo, $numerosProcessados);
                if (!empty($erroresGrupo)) {
                    $errores[$clave] = $erroresGrupo;
                    $erroresCriticos++;
                    Log::warning("Grupo $clave rechazado por errores:", $erroresGrupo);
                    continue;
                }

                // Crear plano
                $plano = $this->crearPlanoDesdeGrupo($grupo);
                $planosCreados++;

                // Marcar número como procesado
                $numerosProcessados[] = $grupo[0]['N°_PLANO'];

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

        // Lookup provincia (búsqueda inteligente)
        $nombreComuna = trim($primerFila['COMUNA']);
        $codigoComuna = $primerFila['CODIGO_COMUNAL']; // Código ya normalizado
        $comuna = $this->buscarComuna($nombreComuna, $codigoComuna);
        $provincia = $comuna ? $comuna->provincia : 'DESCONOCIDA';

        // Extraer mes de fecha
        $mes = $this->extraerMes($primerFila['FECHA']);

        // Limpiar tipo_saneamiento quitando puntos
        $tipoSaneamiento = str_replace('.', '', $primerFila['URBANO/RURAL']);

        return Plano::create([
            'numero_plano' => $primerFila['N°_PLANO'],
            'codigo_region' => $primerFila['CODIGO_REGIONAL'],
            'codigo_comuna' => $primerFila['CODIGO_COMUNAL'],
            'numero_correlativo' => $primerFila['N°_PLANO'], // Por ahora el mismo valor
            'tipo_saneamiento' => $tipoSaneamiento,
            'provincia' => $provincia,
            'comuna' => $nombreComuna,
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
        // Determinar tipo de inmueble usando nueva lógica
        $tipoResult = $this->determinarTipoInmueble($fila);

        // Si hay error en determinación de tipo, no crear el folio
        if (isset($tipoResult['error'])) {
            throw new \Exception("Fila {$fila['NUMERO_FILA']}: {$tipoResult['error']}");
        }

        return PlanoFolio::create([
            'plano_id' => $planoId,
            'folio' => !empty(trim($fila['FOLIO'])) ? trim($fila['FOLIO']) : null,
            'solicitante' => trim($fila['SOLICITANTE']) ?: 'SIN ESPECIFICAR',
            'apellido_paterno' => !empty(trim($fila['PATERNO'])) ? trim($fila['PATERNO']) : null,
            'apellido_materno' => !empty(trim($fila['MATERNO'])) ? trim($fila['MATERNO']) : null,
            'tipo_inmueble' => $tipoResult['tipo'],
            'numero_inmueble' => $tipoResult['numero'],
            'hectareas' => $tipoResult['hectareas'],
            'm2' => $tipoResult['m2'],
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

    private function validarFolioSegunTipo($fila)
    {
        $tipoSaneamiento = str_replace('.', '', $fila['URBANO/RURAL']);
        $esFiscal = in_array($tipoSaneamiento, ['CR', 'CU']);

        // Solo saneamiento requiere FOLIO obligatorio
        if (!$esFiscal && empty(trim($fila['FOLIO']))) {
            return "WARNING: Plano saneamiento sin FOLIO en fila {$fila['NUMERO_FILA']}";
        }

        return null; // Fiscales OK sin folio
    }

    private function determinarTipoInmueble($fila)
    {
        $hectareas = floatval($fila['HA'] ?? 0);
        $m2Hij = intval($fila['M²_HIJ'] ?? 0);
        $m2Sitio = intval($fila['M²_SITIO'] ?? 0);

        if ($hectareas > 0 || $m2Hij > 0) {
            // Es HIJUELA
            return [
                'tipo' => 'HIJUELA',
                'numero' => !empty($fila['HIJ']) ? intval($fila['HIJ']) : 1,
                'hectareas' => $hectareas,
                'm2' => $m2Hij
            ];
        } elseif ($m2Sitio > 0) {
            // Es SITIO
            return [
                'tipo' => 'SITIO',
                'numero' => !empty($fila['SITIO']) ? intval($fila['SITIO']) : 1,
                'hectareas' => null,
                'm2' => $m2Sitio
            ];
        }

        // Caso sin datos de superficie - asignar valores por defecto como SITIO
        Log::info("Fila {$fila['NUMERO_FILA']}: Sin superficie, asignando como SITIO con 0 m²");
        return [
            'tipo' => 'SITIO',
            'numero' => 1,
            'hectareas' => null,
            'm2' => 0
        ];
    }

    private function buscarComuna($nombreComuna, $codigoComuna = null)
    {
        $nombre = trim($nombreComuna);

        // 1. Búsqueda por código si está disponible (más confiable)
        if (!empty($codigoComuna)) {
            $codigo = trim($codigoComuna);
            $comuna = ComunaBiobio::where('codigo', $codigo)->first();
            if ($comuna) {
                Log::info("Comuna encontrada por código: $codigo -> {$comuna->nombre}");
                return $comuna;
            }
        }

        // 2. Búsqueda exacta por nombre
        $comuna = ComunaBiobio::whereRaw('UPPER(nombre) = UPPER(?)', [$nombre])->first();
        if ($comuna) return $comuna;

        // 3. Búsqueda sin considerar acentos y espacios
        $comuna = ComunaBiobio::whereRaw(
            'UPPER(REPLACE(REPLACE(REPLACE(nombre, "Á", "A"), "É", "E"), "Ñ", "N")) LIKE UPPER(?)',
            ["%$nombre%"]
        )->first();

        return $comuna;
    }

    private function normalizarCodigoComuna($codigoOriginal)
    {
        $codigo = trim($codigoOriginal);

        // Si el código tiene más de 3 dígitos y empieza con 8, quitar el 8 inicial
        if (strlen($codigo) > 3 && str_starts_with($codigo, '8')) {
            $codigoNormalizado = substr($codigo, 1); // Quitar primer carácter (8)
            Log::info("Código comuna normalizado: $codigo -> $codigoNormalizado");
            return $codigoNormalizado;
        }

        return $codigo;
    }
}
