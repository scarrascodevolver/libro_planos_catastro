<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionPdf extends Model
{
    protected $table = 'configuracion_pdfs';

    protected $fillable = [
        'ano',
        'ruta_base',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ano' => 'integer'
    ];

    /**
     * Obtener la ruta base para un año específico
     */
    public static function getRutaPorAno($ano)
    {
        return self::where('ano', $ano)
                   ->where('activo', true)
                   ->value('ruta_base');
    }
}
