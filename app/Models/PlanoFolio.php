<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanoFolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'plano_id', 'folio', 'solicitante', 'apellido_paterno', 'apellido_materno',
        'tipo_inmueble', 'numero_inmueble', 'hectareas', 'm2',
        'is_from_matrix', 'matrix_folio'
    ];

    protected $casts = [
        'hectareas' => 'decimal:4',
        'is_from_matrix' => 'boolean',
    ];

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->solicitante . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno);
    }

    public function isHijuela(): bool
    {
        return $this->tipo_inmueble === 'HIJUELA';
    }

    public function isSitio(): bool
    {
        return $this->tipo_inmueble === 'SITIO';
    }

    public function esDeMatrix(): bool
    {
        return $this->is_from_matrix;
    }
}