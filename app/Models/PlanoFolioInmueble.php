<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoFolioInmueble extends Model
{
    use HasFactory;

    protected $table = 'planos_folios_inmuebles';

    protected $fillable = [
        'plano_folio_id',
        'numero_inmueble',
        'tipo_inmueble',
        'hectareas',
        'm2',
    ];

    protected $casts = [
        'hectareas' => 'decimal:4',
        'm2' => 'integer',
    ];

    /**
     * Relación con el folio padre
     */
    public function planoFolio()
    {
        return $this->belongsTo(PlanoFolio::class, 'plano_folio_id');
    }

    /**
     * Obtener etiqueta del tipo con número
     */
    public function getTipoLabelAttribute()
    {
        return $this->tipo_inmueble . ' #' . $this->numero_inmueble;
    }
}
