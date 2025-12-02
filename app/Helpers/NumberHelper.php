<?php

if (!function_exists('normalizarNumero')) {
    /**
     * Normaliza un número en formato chileno (o USA/UK) a formato PHP estándar
     * Maneja:
     * - "1.234,56" (chileno) → 1234.56
     * - "1,234.56" (USA) → 1234.56
     * - "1234.56" → 1234.56
     * - "1234,56" → 1234.56
     *
     * @param string|float|int|null $valor
     * @return float|null
     */
    function normalizarNumero($valor)
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        // Convertir a string y limpiar espacios
        $valor = trim((string) $valor);

        // Eliminar espacios en blanco
        $valor = str_replace(' ', '', $valor);

        // Detectar formato: si tiene punto Y coma, determinar cuál es decimal
        $tienePunto = strpos($valor, '.') !== false;
        $tieneComa = strpos($valor, ',') !== false;

        if ($tienePunto && $tieneComa) {
            // Ambos presentes: el último es el decimal
            $posPunto = strrpos($valor, '.');
            $posComa = strrpos($valor, ',');

            if ($posPunto > $posComa) {
                // Formato USA: "1,234.56"
                $valor = str_replace(',', '', $valor); // quitar comas de miles
            } else {
                // Formato chileno: "1.234,56"
                $valor = str_replace('.', '', $valor); // quitar puntos de miles
                $valor = str_replace(',', '.', $valor); // coma a punto decimal
            }
        } elseif ($tieneComa) {
            // Solo coma: asumir decimal chileno
            $valor = str_replace(',', '.', $valor);
        } elseif ($tienePunto) {
            // Solo punto: podría ser decimal USA o separador miles
            // Si hay más de 3 dígitos después del punto, es separador de miles
            $partes = explode('.', $valor);
            if (count($partes) > 2) {
                // Múltiples puntos: son separadores de miles
                $valor = str_replace('.', '', $valor);
            } elseif (isset($partes[1]) && strlen($partes[1]) > 2) {
                // Más de 2 decimales: probablemente sea separador de miles
                // Ej: "520.210" → 520210
                $valor = str_replace('.', '', $valor);
            }
            // Si tiene exactamente 2 decimales o menos, asumir que es decimal
        }

        // Convertir a float
        return floatval($valor);
    }
}

if (!function_exists('normalizarHectareas')) {
    /**
     * Normaliza hectáreas (siempre decimal con hasta 4 decimales)
     *
     * @param string|float|int|null $valor
     * @return float|null
     */
    function normalizarHectareas($valor)
    {
        $normalizado = normalizarNumero($valor);
        return $normalizado !== null ? round($normalizado, 4) : null;
    }
}

if (!function_exists('normalizarM2')) {
    /**
     * Normaliza metros cuadrados (siempre entero)
     *
     * @param string|float|int|null $valor
     * @return int|null
     */
    function normalizarM2($valor)
    {
        $normalizado = normalizarNumero($valor);
        return $normalizado !== null ? (int) round($normalizado) : null;
    }
}
