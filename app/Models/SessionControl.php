<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SessionControl extends Model
{
    use HasFactory;

    protected $table = 'session_control';

    protected $fillable = [
        'user_id', 'session_id', 'has_control', 'requested_at',
        'granted_at', 'released_at', 'is_active'
    ];

    protected $casts = [
        'has_control' => 'boolean',
        'is_active' => 'boolean',
        'requested_at' => 'datetime',
        'granted_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function quienTieneControl(): ?User
    {
        $control = self::where('has_control', true)
                      ->where('is_active', true)
                      ->first();
        return $control ? $control->user : null;
    }

    public static function hayControlActivo(): bool
    {
        return self::where('has_control', true)
                   ->where('is_active', true)
                   ->exists();
    }

    public static function getProximoCorrelativo(): int
    {
        return 29272;
    }
}