<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatrixImport extends Model
{
    use HasFactory;

    protected $table = 'matrix_import';

    protected $fillable = [
        'folio', 'tipo_inmueble', 'comuna',
        'nombres', 'apellido_paterno', 'apellido_materno',
        'responsable', 'convenio_financiamiento', 'batch_import'
    ];

    public static function buscarPorFolio(string $folio)
    {
        return self::where('folio', $folio)->first();
    }

    public static function getFoliosDisponibles()
    {
        return self::select('folio')->distinct()->orderBy('folio')->pluck('folio');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombres . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno);
    }

    public static function getUltimoBatch(): ?string
    {
        return self::orderBy('created_at', 'desc')->value('batch_import');
    }
}