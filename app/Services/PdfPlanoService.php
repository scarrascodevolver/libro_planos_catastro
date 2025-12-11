<?php

namespace App\Services;

use App\Models\ConfiguracionPdf;
use App\Models\Plano;

class PdfPlanoService
{
    /**
     * Buscar PDF de un plano usando múltiples estrategias
     *
     * @param Plano $plano
     * @return string|null Ruta completa al archivo PDF o null si no se encuentra
     */
    public function buscarPdf(Plano $plano)
    {
        $ano = $plano->ano;
        $numeroPlano = $plano->numero_plano;

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

        // Estrategia 1: Coincidencia exacta
        $archivoExacto = $rutaBase . DIRECTORY_SEPARATOR . $numeroPlano . '.pdf';
        if (file_exists($archivoExacto)) {
            return $archivoExacto;
        }

        // Estrategia 2: Archivos que empiezan con el número de plano
        // Ejemplo: 0810129551SU copia.pdf, 0810129551SU (1).pdf
        $patron1 = $rutaBase . DIRECTORY_SEPARATOR . $numeroPlano . '*.pdf';
        $archivos = glob($patron1);
        if (!empty($archivos)) {
            return $archivos[0];
        }

        // Estrategia 3: Archivos que contienen el número de plano (con cualquier prefijo)
        // Ejemplo: 10810129551SU.pdf
        $patron2 = $rutaBase . DIRECTORY_SEPARATOR . '*' . $numeroPlano . '*.pdf';
        $archivos = glob($patron2);
        if (!empty($archivos)) {
            return $archivos[0];
        }

        // Estrategia 4: Sin el primer carácter (por si tiene "1" extra al inicio)
        // Ejemplo: buscar 810129551SU si el original es 0810129551SU
        if (strlen($numeroPlano) > 1) {
            $numeroSinPrimerCaracter = substr($numeroPlano, 1);
            $patron3 = $rutaBase . DIRECTORY_SEPARATOR . '*' . $numeroSinPrimerCaracter . '*.pdf';
            $archivos = glob($patron3);
            if (!empty($archivos)) {
                return $archivos[0];
            }
        }

        return null;
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
