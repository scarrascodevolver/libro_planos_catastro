<?php

namespace App\Console\Commands;

use App\Models\SessionControl;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ReleaseInactiveSessionControl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:release-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Libera controles de sesión inactivos (sin heartbeat por más de 10 minutos)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Timeout: 30 minutos (realista para ambiente de oficina)
        $timeoutMinutes = 30;
        $cutoffTime = Carbon::now()->subMinutes($timeoutMinutes);

        // Buscar controles activos con last_heartbeat mayor a 10 minutos
        $inactiveControls = SessionControl::where('has_control', true)
            ->where('is_active', true)
            ->where(function($query) use ($cutoffTime) {
                $query->where('last_heartbeat', '<', $cutoffTime)
                      ->orWhereNull('last_heartbeat');
            })
            ->get();

        if ($inactiveControls->isEmpty()) {
            $this->info('No hay controles inactivos para liberar.');
            return 0;
        }

        $count = 0;
        foreach ($inactiveControls as $control) {
            $userName = $control->user ? $control->user->name : 'Usuario desconocido';
            $lastHeartbeat = $control->last_heartbeat
                ? $control->last_heartbeat->diffForHumans()
                : 'nunca';

            $control->update([
                'has_control' => false,
                'is_active' => false,
                'released_at' => now()
            ]);

            $this->info("Control liberado: {$userName} (último heartbeat: {$lastHeartbeat})");
            $count++;
        }

        $this->info("Total de controles liberados: {$count}");
        return 0;
    }
}
