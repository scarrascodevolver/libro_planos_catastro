<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComunaBiobio extends Model
{
    use HasFactory;

    protected $table = 'comunas_biobio';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['codigo', 'nombre', 'provincia'];

    public static function getParaSelect()
    {
        return self::orderBy('nombre')->pluck('nombre', 'codigo');
    }

    public static function getPorProvincia(string $provincia)
    {
        return self::where('provincia', $provincia)->orderBy('nombre')->get();
    }

    public function esConcepcion(): bool
    {
        return $this->provincia === 'Concepción';
    }

    public function getCodigoParaPlano(): string
    {
        return substr($this->codigo, 0, 3);
    }
}