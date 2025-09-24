<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComunaBiobioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comunas = [
            // Provincia de Concepción
            ['codigo' => '101', 'nombre' => 'Concepción', 'provincia' => 'Concepción'],
            ['codigo' => '102', 'nombre' => 'Coronel', 'provincia' => 'Concepción'],
            ['codigo' => '103', 'nombre' => 'Chiguayante', 'provincia' => 'Concepción'],
            ['codigo' => '104', 'nombre' => 'Florida', 'provincia' => 'Concepción'],
            ['codigo' => '105', 'nombre' => 'Hualqui', 'provincia' => 'Concepción'],
            ['codigo' => '106', 'nombre' => 'Lota', 'provincia' => 'Concepción'],
            ['codigo' => '107', 'nombre' => 'Penco', 'provincia' => 'Concepción'],
            ['codigo' => '108', 'nombre' => 'San Pedro de la Paz', 'provincia' => 'Concepción'],
            ['codigo' => '109', 'nombre' => 'Santa Juana', 'provincia' => 'Concepción'],
            ['codigo' => '110', 'nombre' => 'Talcahuano', 'provincia' => 'Concepción'],
            ['codigo' => '111', 'nombre' => 'Tomé', 'provincia' => 'Concepción'],
            ['codigo' => '112', 'nombre' => 'Hualpén', 'provincia' => 'Concepción'],

            // Provincia de Arauco
            ['codigo' => '201', 'nombre' => 'Lebu', 'provincia' => 'Arauco'],
            ['codigo' => '202', 'nombre' => 'Arauco', 'provincia' => 'Arauco'],
            ['codigo' => '203', 'nombre' => 'Cañete', 'provincia' => 'Arauco'],
            ['codigo' => '204', 'nombre' => 'Contulmo', 'provincia' => 'Arauco'],
            ['codigo' => '205', 'nombre' => 'Curanilahue', 'provincia' => 'Arauco'],
            ['codigo' => '206', 'nombre' => 'Los Álamos', 'provincia' => 'Arauco'],
            ['codigo' => '207', 'nombre' => 'Tirúa', 'provincia' => 'Arauco'],

            // Provincia de Biobío
            ['codigo' => '301', 'nombre' => 'Los Ángeles', 'provincia' => 'Biobío'],
            ['codigo' => '302', 'nombre' => 'Antuco', 'provincia' => 'Biobío'],
            ['codigo' => '303', 'nombre' => 'Cabrero', 'provincia' => 'Biobío'],
            ['codigo' => '304', 'nombre' => 'Laja', 'provincia' => 'Biobío'],
            ['codigo' => '305', 'nombre' => 'Mulchén', 'provincia' => 'Biobío'],
            ['codigo' => '306', 'nombre' => 'Nacimiento', 'provincia' => 'Biobío'],
            ['codigo' => '307', 'nombre' => 'Negrete', 'provincia' => 'Biobío'],
            ['codigo' => '308', 'nombre' => 'Quilaco', 'provincia' => 'Biobío'],
            ['codigo' => '309', 'nombre' => 'Quilleco', 'provincia' => 'Biobío'],
            ['codigo' => '310', 'nombre' => 'San Rosendo', 'provincia' => 'Biobío'],
            ['codigo' => '311', 'nombre' => 'Santa Bárbara', 'provincia' => 'Biobío'],
            ['codigo' => '312', 'nombre' => 'Tucapel', 'provincia' => 'Biobío'],
            ['codigo' => '313', 'nombre' => 'Yumbel', 'provincia' => 'Biobío'],
            ['codigo' => '314', 'nombre' => 'Alto Biobío', 'provincia' => 'Biobío'],

            // Provincia de Ñuble
            ['codigo' => '401', 'nombre' => 'Chillán', 'provincia' => 'Ñuble'],
            ['codigo' => '402', 'nombre' => 'Bulnes', 'provincia' => 'Ñuble'],
            ['codigo' => '403', 'nombre' => 'Cobquecura', 'provincia' => 'Ñuble'],
            ['codigo' => '404', 'nombre' => 'Coelemu', 'provincia' => 'Ñuble'],
            ['codigo' => '405', 'nombre' => 'Coihueco', 'provincia' => 'Ñuble'],
            ['codigo' => '406', 'nombre' => 'Chillán Viejo', 'provincia' => 'Ñuble'],
            ['codigo' => '407', 'nombre' => 'El Carmen', 'provincia' => 'Ñuble'],
            ['codigo' => '408', 'nombre' => 'Ninhue', 'provincia' => 'Ñuble'],
            ['codigo' => '409', 'nombre' => 'Ñiquén', 'provincia' => 'Ñuble'],
            ['codigo' => '410', 'nombre' => 'Pemuco', 'provincia' => 'Ñuble'],
            ['codigo' => '411', 'nombre' => 'Pinto', 'provincia' => 'Ñuble'],
            ['codigo' => '412', 'nombre' => 'Portezuelo', 'provincia' => 'Ñuble'],
            ['codigo' => '413', 'nombre' => 'Quillón', 'provincia' => 'Ñuble'],
            ['codigo' => '414', 'nombre' => 'Quirihue', 'provincia' => 'Ñuble'],
            ['codigo' => '415', 'nombre' => 'Ránquil', 'provincia' => 'Ñuble'],
            ['codigo' => '416', 'nombre' => 'San Carlos', 'provincia' => 'Ñuble'],
            ['codigo' => '417', 'nombre' => 'San Fabián', 'provincia' => 'Ñuble'],
            ['codigo' => '418', 'nombre' => 'San Ignacio', 'provincia' => 'Ñuble'],
            ['codigo' => '419', 'nombre' => 'San Nicolás', 'provincia' => 'Ñuble'],
            ['codigo' => '420', 'nombre' => 'Treguaco', 'provincia' => 'Ñuble'],
            ['codigo' => '421', 'nombre' => 'Yungay', 'provincia' => 'Ñuble'],
        ];

        foreach ($comunas as $comuna) {
            DB::table('comunas_biobio')->insert([
                'codigo' => $comuna['codigo'],
                'nombre' => $comuna['nombre'],
                'provincia' => $comuna['provincia'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}