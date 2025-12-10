<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PlanoFolio;
use App\Models\PlanoFolioInmueble;
use Illuminate\Support\Facades\DB;

class LimpiarInmueblesDuplicados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planos:limpiar-inmuebles-duplicados {--dry-run : Simular sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia inmuebles duplicados en planos_folios_inmuebles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 MODO DRY-RUN: No se harán cambios reales');
        } else {
            $this->warn('⚠️  MODO REAL: Se eliminarán inmuebles duplicados');
            if (!$this->confirm('¿Deseas continuar?')) {
                $this->info('Operación cancelada');
                return 0;
            }
        }

        $this->info('Buscando inmuebles duplicados...');

        // Encontrar folios con inmuebles duplicados (mismo numero_inmueble)
        $foliosConDuplicados = DB::table('planos_folios_inmuebles')
            ->select('plano_folio_id', 'numero_inmueble', 'tipo_inmueble', DB::raw('COUNT(*) as count'))
            ->groupBy('plano_folio_id', 'numero_inmueble', 'tipo_inmueble')
            ->having('count', '>', 1)
            ->get();

        if ($foliosConDuplicados->isEmpty()) {
            $this->info('✅ No se encontraron inmuebles duplicados');
            return 0;
        }

        $this->warn("Se encontraron {$foliosConDuplicados->count()} grupos de inmuebles duplicados");

        $totalEliminados = 0;

        foreach ($foliosConDuplicados as $grupo) {
            $folio = PlanoFolio::find($grupo->plano_folio_id);
            if (!$folio) continue;

            // Obtener todos los inmuebles duplicados
            $inmuebles = PlanoFolioInmueble::where('plano_folio_id', $grupo->plano_folio_id)
                ->where('numero_inmueble', $grupo->numero_inmueble)
                ->where('tipo_inmueble', $grupo->tipo_inmueble)
                ->orderBy('created_at', 'desc')
                ->get();

            $this->line("\nFolio {$folio->folio} - {$grupo->tipo_inmueble} #{$grupo->numero_inmueble}:");

            // Mostrar todos los inmuebles
            foreach ($inmuebles as $index => $inmueble) {
                $mantener = $index === 0 ? '✅ MANTENER' : '❌ ELIMINAR';
                $this->line("  [{$mantener}] ID:{$inmueble->id} - {$inmueble->hectareas} ha / {$inmueble->m2} m² - {$inmueble->created_at}");
            }

            // Eliminar todos excepto el más reciente (el primero en la lista)
            if (!$dryRun) {
                $idsAEliminar = $inmuebles->skip(1)->pluck('id')->toArray();
                if (!empty($idsAEliminar)) {
                    PlanoFolioInmueble::whereIn('id', $idsAEliminar)->delete();
                    $totalEliminados += count($idsAEliminar);
                }
            } else {
                $totalEliminados += $inmuebles->count() - 1;
            }
        }

        $this->line('');
        if ($dryRun) {
            $this->info("✅ Se eliminarían {$totalEliminados} inmuebles duplicados");
            $this->info('Ejecuta sin --dry-run para aplicar los cambios');
        } else {
            $this->info("✅ Se eliminaron {$totalEliminados} inmuebles duplicados");
        }

        return 0;
    }
}
