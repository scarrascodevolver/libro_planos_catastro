<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\PlanoFolio;
use App\Models\ComunaBiobio;
use App\Models\SessionControl;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
                'totalFilas' => count($grupos), // Cantidad de PLANOS (grupos) no filas Excel
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

        // VALIDAR CONTROL DE SESIÓN
        $user = Auth::user();

        // Solo usuarios con rol "registro" pueden importar
        if (!$user->isRegistro()) {
            return response()->json([
                'success' => false,
                'message' => 'Solo usuarios con rol "registro" pueden importar planos históricos'
            ], 403);
        }

        // Verificar que el usuario tiene control activo
        $control = SessionControl::where('user_id', $user->id)
            ->where('has_control', true)
            ->where('is_active', true)
            ->first();

        if (!$control) {
            $quienTiene = SessionControl::quienTieneControl();
            $mensaje = $quienTiene
                ? "El control lo tiene actualmente: {$quienTiene->name}. Debes solicitarlo para poder importar."
                : "No tienes control de numeración. Debes solicitarlo primero para poder importar.";

            return response()->json([
                'success' => false,
                'message' => $mensaje,
                'requiere_control' => true
            ], 403);
        }

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

            // SIEMPRE hacer commit (aunque haya errores críticos)
            // Los errores críticos solo rechazan planos específicos, no cancelan TODO
            DB::commit();

            // Determinar si fue éxito o no
            if ($resultado['planos_creados'] > 0) {
                // Se importó al menos 1 plano → ÉXITO
                $message = $resultado['planos_creados'] . ' plano(s) importado(s) exitosamente';
                if ($resultado['errores_criticos'] > 0) {
                    $message .= ' (' . $resultado['errores_criticos'] . ' plano(s) rechazado(s))';
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'resultado' => $resultado
                ]);
            } else {
                // NO se importó NADA → ERROR
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo importar ningún plano. Todos tienen errores críticos.',
                    'resultado' => $resultado
                ], 422);
            }

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

    /**
     * Detecta el formato del Excel según la estructura de la primera fila de datos
     *
     * @param array $primeraFilaDatos Primera fila con datos (sin header)
     * @return string 'USUARIO_COMPACTO' (08301 junto) o 'SEPARADO' (08 | 301)
     */
    private function detectarFormato($primeraFilaDatos)
    {
        $celdaA = trim($primeraFilaDatos[0] ?? '');

        // Si columna A tiene 5 dígitos → formato usuario compacto (08301)
        if (preg_match('/^\d{5}$/', $celdaA)) {
            return 'USUARIO_COMPACTO';
        }

        // Si columna A tiene 2 dígitos → formato separado (08)
        if (preg_match('/^\d{1,2}$/', $celdaA)) {
            return 'SEPARADO';
        }

        // Fallback: intentar detectar por longitud
        if (strlen($celdaA) >= 4 && strlen($celdaA) <= 5) {
            return 'USUARIO_COMPACTO';
        }

        return 'SEPARADO';
    }

    private function agruparDatos($datos)
    {
        $grupos = [];

        // Detectar formato con la primera fila de datos (no vacía)
        $formato = 'SEPARADO'; // Default
        foreach ($datos as $fila) {
            if (!empty(array_filter($fila))) {
                $formato = $this->detectarFormato($fila);
                break; // Detectar solo con la primera fila válida
            }
        }

        foreach ($datos as $index => $fila) {
            // Saltear filas vacías
            if (empty(array_filter($fila))) continue;

            // Mapear índices de columnas pasando el formato detectado
            $registro = $this->mapearFilaExcel($fila, $index, $formato);

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

    private function mapearFilaExcel($fila, $numeroFila, $formato = 'SEPARADO')
    {
        if ($formato === 'USUARIO_COMPACTO') {
            // FORMATO USUARIO: Código regional+comunal junto en columna A (08301)
            // Columnas corridas 1 posición a la izquierda

            // Separar código regional (08) y comunal (301) de columna A
            $codigoCompleto = str_pad(trim($fila[0] ?? ''), 5, '0', STR_PAD_LEFT);
            $codigoRegional = substr($codigoCompleto, 0, 2);  // Primeros 2 dígitos
            $codigoComunal = substr($codigoCompleto, 2, 3);   // Últimos 3 dígitos

            // Armar número de plano completo: 08 + 301 + 29270 + CU = 0830129270CU
            $numeroCorrelativo = trim($fila[1] ?? '');
            $tipoSaneamiento = str_replace('.', '', strtoupper(trim($fila[2] ?? ''))); // Quitar puntos (S.R. → SR)
            $numeroPlano = $codigoRegional . $codigoComunal . $numeroCorrelativo . $tipoSaneamiento;

            return [
                'NUMERO_FILA' => $numeroFila + 1,
                'CODIGO_REGIONAL' => $codigoRegional,
                'CODIGO_COMUNAL' => $this->normalizarCodigoComuna($codigoComunal),
                'N°_PLANO' => $numeroPlano,
                'URBANO/RURAL' => $tipoSaneamiento,
                'FOLIO' => $fila[3] ?? '',              // Col D (era E)
                'SOLICITANTE' => $fila[4] ?? '',        // Col E (era F)
                'PATERNO' => $fila[5] ?? '',            // Col F (era G)
                'MATERNO' => $fila[6] ?? '',            // Col G (era H)
                'COMUNA' => $fila[7] ?? '',             // Col H (era I)
                'HIJ' => intval($fila[8] ?? 0),         // Col I (era J)
                'HA' => floatval($fila[9] ?? 0),        // Col J (era K)
                'M²_HIJ' => floatval($fila[10] ?? 0),   // Col K (era L)
                'SITIO' => intval($fila[11] ?? 0),      // Col L (era M)
                'M²_SITIO' => floatval($fila[12] ?? 0), // Col M (era N)
                'FECHA' => $fila[13] ?? '',             // Col N (era O)
                'AÑO' => intval($fila[14] ?? 0),        // Col O (era P)
                'Responsable' => $fila[15] ?? '',       // Col P (era Q)
                'PROYECTO' => $fila[16] ?? '',          // Col Q (era R)
                'PROVIDENCIA' => $fila[17] ?? '',       // Col R (era S)
                'ARCHIVO' => $fila[18] ?? '',           // Col S (era T)
                'OBSERVACION' => $fila[19] ?? '',       // Col T (era U)
                'TUBO' => $fila[20] ?? '',              // Col U (era V)
                'TELA' => $fila[21] ?? '',              // Col V (era W)
                'ARCHIVO_DIGITAL' => $fila[22] ?? ''    // Col W (era X)
            ];

        } else {
            // FORMATO SEPARADO (actual): Código regional y comunal en columnas A y B
            // 08 | 301 | 29270 | CU | ...

            // Armar número de plano completo si no viene armado
            $numeroPlanoOriginal = trim($fila[2] ?? '');
            $codigoRegional = trim($fila[0] ?? '');
            $codigoComunal = $this->normalizarCodigoComuna(trim($fila[1] ?? ''));
            $tipoSaneamiento = str_replace('.', '', strtoupper(trim($fila[3] ?? ''))); // Quitar puntos (S.R. → SR)

            // Si el número de plano está vacío o es solo el correlativo, armarlo completo
            if (empty($numeroPlanoOriginal) || !preg_match('/[A-Z]{2}$/', $numeroPlanoOriginal)) {
                $numeroPlano = $codigoRegional . $codigoComunal . $numeroPlanoOriginal . $tipoSaneamiento;
            } else {
                $numeroPlano = $numeroPlanoOriginal;
            }

            return [
                'NUMERO_FILA' => $numeroFila + 1,
                'CODIGO_REGIONAL' => $codigoRegional,
                'CODIGO_COMUNAL' => $codigoComunal,
                'N°_PLANO' => $numeroPlano,
                'URBANO/RURAL' => $tipoSaneamiento,
                'FOLIO' => $fila[4] ?? '',
                'SOLICITANTE' => $fila[5] ?? '',
                'PATERNO' => $fila[6] ?? '',
                'MATERNO' => $fila[7] ?? '',
                'COMUNA' => $fila[8] ?? '',
                'HIJ' => intval($fila[9] ?? 0),
                'HA' => floatval($fila[10] ?? 0),
                'M²_HIJ' => floatval($fila[11] ?? 0),   // Primera columna M² - Con decimales
                'SITIO' => intval($fila[12] ?? 0),
                'M²_SITIO' => floatval($fila[13] ?? 0), // Segunda columna M² - Con decimales
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

        // Validar tipo saneamiento NO vacío (campo crítico obligatorio)
        $tipoSaneamiento = trim($primerFila['URBANO/RURAL'] ?? '');
        if (empty($tipoSaneamiento)) {
            $errores[] = "CRÍTICO: Tipo de plano (SR/SU/CR/CU) está VACÍO";
        } elseif (!in_array($tipoSaneamiento, ['SR', 'SU', 'CR', 'CU'])) {
            $errores[] = "CRÍTICO: Tipo de plano inválido '{$tipoSaneamiento}' (debe ser SR, SU, CR o CU)";
        }

        // Validar número plano único en BD
        $existeEnBD = Plano::where('numero_plano', $numeroPlano)->exists();
        if ($existeEnBD) {
            $errores[] = "CRÍTICO: Número de plano {$numeroPlano} ya existe en BD";
        }

        // Validar número plano único en este lote de importación
        if (in_array($numeroPlano, $numerosProcessados)) {
            $errores[] = "CRÍTICO: Número de plano {$numeroPlano} duplicado en este archivo";
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
        $errores = []; // Errores críticos con info detallada
        $warnings = []; // Warnings con info detallada
        $erroresCriticos = 0;
        $numerosProcessados = []; // Track números ya procesados en este lote

        foreach ($grupos as $clave => $grupo) {
            try {
                $primerFila = $grupo[0];

                // Validar grupo antes de procesar
                $erroresGrupo = $this->validarGrupo($grupo, $numerosProcessados);
                $warningsGrupo = []; // Warnings detectados durante validación

                if (!empty($erroresGrupo)) {
                    // Separar WARNINGS de ERRORES CRÍTICOS
                    $erroresCriticosGrupo = [];

                    foreach ($erroresGrupo as $error) {
                        // Si contiene "CRÍTICO" o "ya existe" o "duplicado" → es crítico
                        if (stripos($error, 'CRÍTICO') !== false ||
                            stripos($error, 'ya existe') !== false ||
                            stripos($error, 'duplicado') !== false) {
                            $erroresCriticosGrupo[] = $error;
                        } else {
                            $warningsGrupo[] = $error;
                        }
                    }

                    // Preparar info del plano para mostrar al usuario
                    $infoPlano = [
                        'numero_plano' => $primerFila['N°_PLANO'],
                        'fila_excel' => $primerFila['NUMERO_FILA'],
                        'comuna' => $primerFila['COMUNA'],
                        'solicitante' => $primerFila['SOLICITANTE'],
                        'folio' => $primerFila['FOLIO'] ?: '[VACÍO]',
                        'tipo' => $primerFila['URBANO/RURAL']
                    ];

                    if (!empty($erroresCriticosGrupo)) {
                        // Errores críticos → rechazar grupo
                        $errores[] = array_merge($infoPlano, [
                            'errores' => $erroresCriticosGrupo
                        ]);
                        $erroresCriticos++;
                        Log::error("Grupo $clave rechazado por errores críticos:", $erroresCriticosGrupo);
                        continue;
                    }
                }

                // DETECTAR WARNINGS ADICIONALES (superficies vacías, datos incompletos)
                $warningsAdicionales = $this->detectarWarningsGrupo($grupo);
                $warningsGrupo = array_merge($warningsGrupo, $warningsAdicionales);

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

                // Si hay warnings para este grupo → agregar al reporte
                if (!empty($warningsGrupo)) {
                    $infoPlano = [
                        'numero_plano' => $primerFila['N°_PLANO'],
                        'fila_excel' => $primerFila['NUMERO_FILA'],
                        'comuna' => $primerFila['COMUNA'],
                        'solicitante' => $primerFila['SOLICITANTE'],
                        'folio' => $primerFila['FOLIO'] ?: '[VACÍO]',
                        'tipo' => $primerFila['URBANO/RURAL']
                    ];
                    $warnings[] = array_merge($infoPlano, [
                        'advertencias' => $warningsGrupo
                    ]);
                }

            } catch (\Exception $e) {
                $primerFila = $grupo[0] ?? [];
                $errores[] = [
                    'numero_plano' => $primerFila['N°_PLANO'] ?? 'Desconocido',
                    'fila_excel' => $primerFila['NUMERO_FILA'] ?? '?',
                    'comuna' => $primerFila['COMUNA'] ?? '?',
                    'solicitante' => $primerFila['SOLICITANTE'] ?? '?',
                    'folio' => $primerFila['FOLIO'] ?? '[VACÍO]',
                    'tipo' => $primerFila['URBANO/RURAL'] ?? '?',
                    'errores' => ["Error al procesar: " . $e->getMessage()]
                ];
                $erroresCriticos++;
                Log::error("Error procesando grupo $clave: " . $e->getMessage());
            }
        }

        return [
            'planos_creados' => $planosCreados,
            'folios_creados' => $foliosCreados,
            'errores' => $errores, // Array de objetos con info detallada
            'warnings' => $warnings, // Array de objetos con info detallada
            'errores_criticos' => $erroresCriticos
        ];
    }

    /**
     * Detecta warnings no críticos en el grupo (superficies vacías, datos incompletos)
     */
    private function detectarWarningsGrupo($grupo)
    {
        $warnings = [];

        foreach ($grupo as $fila) {
            // Warning: Sin superficie
            $hayHectareas = !empty($fila['HA']) && floatval($fila['HA']) > 0;
            $hayM2Hij = !empty($fila['M²_HIJ']) && floatval($fila['M²_HIJ']) > 0;
            $hayM2Sitio = !empty($fila['M²_SITIO']) && floatval($fila['M²_SITIO']) > 0;

            if (!$hayHectareas && !$hayM2Hij && !$hayM2Sitio) {
                $warnings[] = "Folio {$fila['FOLIO']}: Sin superficie (Hectáreas y M² vacíos)";
            }

            // Warning: Solicitante vacío
            if (empty(trim($fila['SOLICITANTE']))) {
                $warnings[] = "Folio {$fila['FOLIO']}: Solicitante vacío";
            }

            // Warning: Apellidos vacíos (solo si no es FISCO)
            $solicitante = strtoupper(trim($fila['SOLICITANTE']));
            if ($solicitante !== 'FISCO' && $solicitante !== 'FISCO DE CHILE') {
                if (empty(trim($fila['PATERNO']))) {
                    $warnings[] = "Folio {$fila['FOLIO']}: Apellido paterno vacío";
                }
            }
        }

        return array_unique($warnings);
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

        // Extraer número correlativo (SOLO el correlativo, sin región, comuna, ni tipo)
        $numeroCorrelativo = $this->extraerNumeroCorrelativo(
            $primerFila['N°_PLANO'],
            $primerFila['CODIGO_REGIONAL'],
            $primerFila['CODIGO_COMUNAL'],
            $tipoSaneamiento
        );

        return Plano::create([
            'numero_plano' => $primerFila['N°_PLANO'],
            'codigo_region' => $primerFila['CODIGO_REGIONAL'],
            'codigo_comuna' => $primerFila['CODIGO_COMUNAL'],
            'numero_correlativo' => $numeroCorrelativo,
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
            'is_historical' => true, // Marcar como plano histórico
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Extrae SOLO el número correlativo del número de plano completo
     *
     * Ejemplo: 0820529872SR → 29872
     * - Quita código regional (08)
     * - Quita código comunal (205)
     * - Quita tipo saneamiento (SR)
     *
     * @param string $numeroCompleto Número completo (0820529872SR)
     * @param string $codigoRegional Código región (08)
     * @param string $codigoComunal Código comuna (205)
     * @param string $tipoSaneamiento Tipo (SR/SU/CR/CU)
     * @return int Número correlativo puro (29872)
     */
    private function extraerNumeroCorrelativo($numeroCompleto, $codigoRegional, $codigoComunal, $tipoSaneamiento)
    {
        // Eliminar código regional del inicio (2 dígitos)
        $sinRegion = substr($numeroCompleto, 2);

        // Eliminar código comunal (3 dígitos)
        $sinComuna = substr($sinRegion, 3);

        // Eliminar tipo saneamiento del final (2 letras)
        $soloCorrelativo = substr($sinComuna, 0, -2);

        // Convertir a integer
        $correlativo = intval($soloCorrelativo);

        return $correlativo;
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

        // Normalizar entrada (quitar espacios y convertir a mayúsculas)
        $mesTexto = strtoupper(trim($fecha));

        // Mapeo de nombres completos a abreviaciones
        $mesesMap = [
            'ENERO' => 'ENE',
            'FEBRERO' => 'FEB',
            'MARZO' => 'MAR',
            'ABRIL' => 'ABR',
            'MAYO' => 'MAY',
            'JUNIO' => 'JUN',
            'JULIO' => 'JUL',
            'AGOSTO' => 'AGO',
            'SEPTIEMBRE' => 'SEP',
            'OCTUBRE' => 'OCT',
            'NOVIEMBRE' => 'NOV',
            'DICIEMBRE' => 'DIC'
        ];

        // Si ya es una abreviación válida, retornarla
        if (in_array($mesTexto, array_values($mesesMap))) {
            return $mesTexto;
        }

        // Buscar en el mapeo de nombres completos
        if (isset($mesesMap[$mesTexto])) {
            return $mesesMap[$mesTexto];
        }

        // Intentar parsear como fecha si no es texto de mes
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
        $m2Hij = floatval($fila['M²_HIJ'] ?? 0);
        $m2Sitio = floatval($fila['M²_SITIO'] ?? 0);

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
            return $codigoNormalizado;
        }

        return $codigo;
    }
}
