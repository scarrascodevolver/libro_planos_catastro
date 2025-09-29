<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionControl;
use App\Models\Plano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionControlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getStatus()
    {
        $user = Auth::user();

        if (!$user->isRegistro()) {
            return response()->json([
                'hasAccess' => false,
                'message' => 'Solo usuarios con rol "registro" pueden crear planos'
            ]);
        }

        $activeControl = SessionControl::quienTieneControl();
        $userHasControl = $activeControl && $activeControl->id === $user->id;

        $proximoCorrelativo = SessionControl::getProximoCorrelativo();
        $proximoNumero = $this->generarProximoNumero($proximoCorrelativo);

        return response()->json([
            'hasAccess' => true,
            'hasControl' => $userHasControl,
            'whoHasControl' => $activeControl ? $activeControl->name : null,
            'proximoCorrelativo' => $proximoCorrelativo,
            'proximoNumero' => $proximoNumero,
            'canRequest' => !SessionControl::hayControlActivo()
        ]);
    }

    public function requestControl()
    {
        $user = Auth::user();

        if (!$user->isRegistro()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para solicitar control'
            ], 403);
        }

        if (SessionControl::hayControlActivo()) {
            return response()->json([
                'success' => false,
                'message' => 'Otro usuario ya tiene el control activo'
            ]);
        }

        // Desactivar controles anteriores del usuario
        SessionControl::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'released_at' => now()]);

        // Crear nuevo control
        SessionControl::create([
            'user_id' => $user->id,
            'session_id' => Session::getId(),
            'has_control' => true,
            'requested_at' => now(),
            'granted_at' => now(),
            'is_active' => true
        ]);

        $proximoCorrelativo = SessionControl::getProximoCorrelativo();
        $proximoNumero = $this->generarProximoNumero($proximoCorrelativo);

        return response()->json([
            'success' => true,
            'message' => 'Control obtenido correctamente',
            'proximoCorrelativo' => $proximoCorrelativo,
            'proximoNumero' => $proximoNumero
        ]);
    }

    public function releaseControl()
    {
        $user = Auth::user();

        $control = SessionControl::where('user_id', $user->id)
            ->where('has_control', true)
            ->where('is_active', true)
            ->first();

        if (!$control) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes control activo'
            ]);
        }

        $control->update([
            'has_control' => false,
            'is_active' => false,
            'released_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Control liberado correctamente'
        ]);
    }

    public function consumeCorrelativo()
    {
        $user = Auth::user();

        if (!$user->isRegistro()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos'
            ], 403);
        }

        $control = SessionControl::where('user_id', $user->id)
            ->where('has_control', true)
            ->where('is_active', true)
            ->first();

        if (!$control) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes control activo'
            ]);
        }

        $proximoCorrelativo = SessionControl::getProximoCorrelativo();
        $proximoNumero = $this->generarProximoNumero($proximoCorrelativo);

        return response()->json([
            'success' => true,
            'correlativo' => $proximoCorrelativo,
            'numeroCompleto' => $proximoNumero
        ]);
    }


    private function generarProximoNumero(int $correlativo, string $codigoComuna = '303', string $tipo = 'SU'): string
    {
        // Formato: 08 + codigo_comuna + correlativo + tipo
        // Ejemplo: 0830329272SU
        return '08' . $codigoComuna . $correlativo . $tipo;
    }

    public function generarNumeroPlano(Request $request)
    {
        $request->validate([
            'codigo_comuna' => 'required|string|size:3',
            'tipo_plano' => 'required|string|size:2|in:SU,SR,CU,CR'
        ]);

        $user = Auth::user();

        if (!$user->isRegistro()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos'
            ], 403);
        }

        $control = SessionControl::where('user_id', $user->id)
            ->where('has_control', true)
            ->where('is_active', true)
            ->first();

        if (!$control) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes control activo para generar números'
            ]);
        }

        $correlativo = $this->getProximoCorrelativo();
        $numeroCompleto = $this->generarProximoNumero(
            $correlativo,
            $request->codigo_comuna,
            $request->tipo_plano
        );

        // Verificar que no exista (doble seguridad)
        if (Plano::where('numero_plano', $numeroCompleto)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Error: número ya existe, reintenta'
            ]);
        }

        return response()->json([
            'success' => true,
            'numeroPlano' => $numeroCompleto,
            'correlativo' => $correlativo
        ]);
    }

    public function heartbeat()
    {
        $user = Auth::user();

        if (!$user->isRegistro()) {
            return response()->json(['active' => false]);
        }

        $control = SessionControl::where('user_id', $user->id)
            ->where('has_control', true)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'active' => !!$control,
            'proximoCorrelativo' => $control ? $this->getProximoCorrelativo() : null
        ]);
    }
}