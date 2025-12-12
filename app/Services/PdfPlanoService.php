<?php

namespace App\Services;

use App\Models\ConfiguracionPdf;
use App\Models\Plano;

class PdfPlanoService
{
    /**
     * Buscar PDF de un plano usando folios con múltiples estrategias
     *
     * @param Plano $plano
     * @return string|null Ruta completa al archivo PDF o null si no se encuentra
     */
    public function buscarPdf(Plano $plano)
    {
        $ano = $plano->ano;

        // Obtener ruta base del año
        $rutaBase = ConfiguracionPdf::getRutaPorAno($ano);

        // Si no hay configuración para este año, retornar null
        if (!$rutaBase) {
            return null;
        }

        // Verificar que el directorio exista
        if (!is_dir($rutaBase)) {
            return null;
        }

        // Obtener todos los folios del plano
        $folios = $plano->folios->pluck('folio')->filter()->toArray();

        if (empty($folios)) {
            return null; // No hay folios para buscar
        }

        // ESTRATEGIA 1: Buscar archivo que contenga TODOS los folios (plano múltiple completo)
        // Solo si el plano tiene más de 1 folio
        if (count($folios) > 1) {
            $archivoPorTodos = $this->buscarPorTodosLosFolios($rutaBase, $folios);
            if ($archivoPorTodos) {
                return $archivoPorTodos;
            }
        }

        // ESTRATEGIA 2: Buscar archivo que contenga AL MENOS UNO de los folios
        foreach ($folios as $folio) {
            // 2.1: Buscar coincidencia exacta individual: 5465.pdf
            $archivoExacto = $rutaBase . DIRECTORY_SEPARATOR . $folio . '.pdf';
            if (file_exists($archivoExacto)) {
                return $archivoExacto;
            }

            // 2.2: Buscar en archivos múltiples que contengan este folio
            $archivoMultiple = $this->buscarEnMultiples($rutaBase, $folio);
            if ($archivoMultiple) {
                return $archivoMultiple;
            }
        }

        return null;
    }

    /**
     * Buscar archivo PDF que contenga TODOS los folios del plano
     *
     * @param string $rutaBase
     * @param array $folios
     * @return string|null
     */
    private function buscarPorTodosLosFolios($rutaBase, $folios)
    {
        // Obtener todos los PDFs en la carpeta
        $todosPdfs = glob($rutaBase . DIRECTORY_SEPARATOR . '*.pdf');

        foreach ($todosPdfs as $pdf) {
            $nombreArchivo = basename($pdf, '.pdf');

            // Normalizar el nombre para tolerancia a errores
            $nombreNormalizado = $this->normalizarNombre($nombreArchivo);

            // Verificar si el archivo contiene todos los folios
            $todosPresentes = true;
            foreach ($folios as $folio) {
                if (!$this->folioEstaEnNombre($nombreNormalizado, $folio)) {
                    $todosPresentes = false;
                    break;
                }
            }

            if ($todosPresentes) {
                return $pdf;
            }
        }

        return null;
    }

    /**
     * Buscar en archivos múltiples que contengan un folio específico
     *
     * @param string $rutaBase
     * @param string $folio
     * @return string|null
     */
    private function buscarEnMultiples($rutaBase, $folio)
    {
        // Primero intentar búsqueda con patrones específicos (más eficiente)
        $patrones = [
            $rutaBase . DIRECTORY_SEPARATOR . $folio . '-*.pdf',        // Inicio: 5465-*.pdf
            $rutaBase . DIRECTORY_SEPARATOR . '*-' . $folio . '-*.pdf',  // Medio: *-5465-*.pdf
            $rutaBase . DIRECTORY_SEPARATOR . '*-' . $folio . '.pdf',    // Final: *-5465.pdf
        ];

        foreach ($patrones as $patron) {
            $archivos = glob($patron);
            if (!empty($archivos)) {
                foreach ($archivos as $archivo) {
                    $nombreArchivo = basename($archivo, '.pdf');
                    $nombreNormalizado = $this->normalizarNombre($nombreArchivo);
                    if ($this->folioEstaEnNombre($nombreNormalizado, $folio)) {
                        return $archivo;
                    }
                }
            }
        }

        // Si no encuentra con patrones exactos, buscar con variaciones (tolerancia a errores)
        return $this->buscarConVariaciones($rutaBase, $folio);
    }

    /**
     * Buscar con tolerancia a errores (espacios, "copia", etc.)
     *
     * @param string $rutaBase
     * @param string $folio
     * @return string|null
     */
    private function buscarConVariaciones($rutaBase, $folio)
    {
        // Obtener todos los PDFs que contengan el folio en alguna parte del nombre
        $patron = $rutaBase . DIRECTORY_SEPARATOR . '*' . $folio . '*.pdf';
        $archivos = glob($patron);

        foreach ($archivos as $archivo) {
            $nombreArchivo = basename($archivo, '.pdf');

            // Normalizar nombre (quitar espacios extras, palabras comunes)
            $nombreNormalizado = $this->normalizarNombre($nombreArchivo);

            // Verificar si el folio está presente
            if ($this->folioEstaEnNombre($nombreNormalizado, $folio)) {
                return $archivo;
            }
        }

        return null;
    }

    /**
     * Normalizar nombre de archivo para tolerancia a errores
     * Quita espacios extras, palabras como "copia", "(1)", etc.
     *
     * @param string $nombre
     * @return string
     */
    private function normalizarNombre($nombre)
    {
        // Quitar extensión si la tiene
        $nombre = str_replace('.pdf', '', $nombre);

        // Quitar palabras comunes: copia, copy, (1), (2), etc.
        $palabrasComunes = [
            'copia', 'copy', '\(1\)', '\(2\)', '\(3\)', '\(4\)', '\(5\)',
            'final', 'version', 'nuevo', 'new', 'v1', 'v2', 'v3'
        ];

        foreach ($palabrasComunes as $palabra) {
            $nombre = preg_replace('/\s*' . $palabra . '\s*/i', '', $nombre);
        }

        // Normalizar espacios (reemplazar múltiples espacios por uno solo)
        $nombre = preg_replace('/\s+/', ' ', $nombre);

        // Quitar espacios alrededor de guiones
        $nombre = preg_replace('/\s*-\s*/', '-', $nombre);

        // Quitar espacios al inicio y final
        $nombre = trim($nombre);

        return $nombre;
    }

    /**
     * Verificar si un folio está presente en el nombre del archivo (coincidencia exacta)
     * Evita coincidencias parciales: buscar "5465" no debe coincidir con "54651"
     *
     * @param string $nombreArchivo
     * @param string $folio
     * @return bool
     */
    private function folioEstaEnNombre($nombreArchivo, $folio)
    {
        // Separar por guiones
        $partes = explode('-', $nombreArchivo);

        // Verificar si el folio está en alguna de las partes (coincidencia exacta)
        foreach ($partes as $parte) {
            // Normalizar la parte (quitar espacios)
            $parte = trim($parte);

            if ($parte === $folio) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si existe PDF para un plano
     *
     * @param Plano $plano
     * @return bool
     */
    public function existePdf(Plano $plano)
    {
        return $this->buscarPdf($plano) !== null;
    }

    /**
     * Obtener el nombre del archivo PDF sin ruta
     *
     * @param Plano $plano
     * @return string|null
     */
    public function getNombreArchivo(Plano $plano)
    {
        $ruta = $this->buscarPdf($plano);

        if (!$ruta) {
            return null;
        }

        return basename($ruta);
    }
}
