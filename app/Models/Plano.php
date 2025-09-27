<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plano extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_plano', 'codigo_region', 'codigo_comuna', 'numero_correlativo',
        'tipo_saneamiento', 'provincia', 'comuna', 'mes', 'ano',
        'responsable', 'proyecto', 'providencia', 'total_hectareas', 'total_m2',
        'cantidad_folios', 'observaciones', 'archivo', 'tubo', 'tela',
        'archivo_digital', 'created_by'
    ];

    protected $casts = [
        'total_hectareas' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function folios()
    {
        return $this->hasMany(PlanoFolio::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getMesAttribute(): string
    {
        $meses = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
        return $meses[$this->created_at->month - 1];
    }

    public function getAnoAttribute(): int
    {
        return $this->created_at->year;
    }

    public function getDisplayFoliosAttribute(): string
    {
        $count = $this->folios->count();
        if ($count <= 2) {
            return $this->folios->pluck('folio')->join(', ');
        }
        $first_two = $this->folios->take(2)->pluck('folio')->join(', ');
        $remaining = $count - 2;
        return $first_two . " +{$remaining} más";
    }

    // Recalcular totales basados en folios relacionados
    public function getTotalHectareasCalculadaAttribute(): ?float
    {
        return $this->folios->sum('hectareas') ?: null;
    }

    public function getTotalM2CalculadoAttribute(): int
    {
        return $this->folios->sum('m2') ?: 0;
    }

    public function getCantidadFoliosCalculadaAttribute(): int
    {
        return $this->folios->count();
    }
}