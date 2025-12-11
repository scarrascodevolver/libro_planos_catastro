<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracionPdf;
use Illuminate\Support\Facades\DB;

class ConfiguracionPdfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder crea configuraciones de ejemplo para los PDFs de planos.
     * Deberás modificar las rutas según tu estructura de carpetas.
     */
    public function run()
    {
        // Limpiar tabla antes de insertar (opcional)
        // ConfiguracionPdf::truncate();

        $configuraciones = [
            [
                'ano' => 2025,
                'ruta_base' => 'Z:\Planos\2025',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ano' => 2024,
                'ruta_base' => 'Z:\Planos\2024',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ano' => 2023,
                'ruta_base' => 'Z:\Planos\2023',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ano' => 2022,
                'ruta_base' => 'Z:\Planos\2022',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ano' => 2021,
                'ruta_base' => 'Z:\Planos\2021',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($configuraciones as $config) {
            ConfiguracionPdf::updateOrCreate(
                ['ano' => $config['ano']], // Buscar por año
                $config // Crear o actualizar con estos datos
            );
        }

        $this->command->info('✅ Configuraciones de PDFs creadas exitosamente');
        $this->command->info('⚠️  IMPORTANTE: Verifica que las rutas configuradas sean correctas para tu entorno');
    }
}
