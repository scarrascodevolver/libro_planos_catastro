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
            ['codigo' => '101', 'nombre' => 'CONCEPCION', 'provincia' => 'CONCEPCION'],
            ['codigo' => '102', 'nombre' => 'CORONEL', 'provincia' => 'CONCEPCION'],
            ['codigo' => '103', 'nombre' => 'CHIGUAYANTE', 'provincia' => 'CONCEPCION'],
            ['codigo' => '104', 'nombre' => 'FLORIDA', 'provincia' => 'CONCEPCION'],
            ['codigo' => '105', 'nombre' => 'HUALQUI', 'provincia' => 'CONCEPCION'],
            ['codigo' => '106', 'nombre' => 'LOTA', 'provincia' => 'CONCEPCION'],
            ['codigo' => '107', 'nombre' => 'PENCO', 'provincia' => 'CONCEPCION'],
            ['codigo' => '108', 'nombre' => 'SAN PEDRO DE LA PAZ', 'provincia' => 'CONCEPCION'],
            ['codigo' => '109', 'nombre' => 'SANTA JUANA', 'provincia' => 'CONCEPCION'],
            ['codigo' => '110', 'nombre' => 'TALCAHUANO', 'provincia' => 'CONCEPCION'],
            ['codigo' => '111', 'nombre' => 'TOME', 'provincia' => 'CONCEPCION'],
            ['codigo' => '112', 'nombre' => 'HUALPEN', 'provincia' => 'CONCEPCION'],

            // Provincia de Arauco
            ['codigo' => '201', 'nombre' => 'LEBU', 'provincia' => 'ARAUCO'],
            ['codigo' => '202', 'nombre' => 'ARAUCO', 'provincia' => 'ARAUCO'],
            ['codigo' => '203', 'nombre' => 'CANETE', 'provincia' => 'ARAUCO'],
            ['codigo' => '204', 'nombre' => 'CONTULMO', 'provincia' => 'ARAUCO'],
            ['codigo' => '205', 'nombre' => 'CURANILAHUE', 'provincia' => 'ARAUCO'],
            ['codigo' => '206', 'nombre' => 'LOS ALAMOS', 'provincia' => 'ARAUCO'],
            ['codigo' => '207', 'nombre' => 'TIRUA', 'provincia' => 'ARAUCO'],

            // Provincia de Biobío
            ['codigo' => '301', 'nombre' => 'LOS ANGELES', 'provincia' => 'BIOBIO'],
            ['codigo' => '302', 'nombre' => 'ANTUCO', 'provincia' => 'BIOBIO'],
            ['codigo' => '303', 'nombre' => 'CABRERO', 'provincia' => 'BIOBIO'],
            ['codigo' => '304', 'nombre' => 'LAJA', 'provincia' => 'BIOBIO'],
            ['codigo' => '305', 'nombre' => 'MULCHEN', 'provincia' => 'BIOBIO'],
            ['codigo' => '306', 'nombre' => 'NACIMIENTO', 'provincia' => 'BIOBIO'],
            ['codigo' => '307', 'nombre' => 'NEGRETE', 'provincia' => 'BIOBIO'],
            ['codigo' => '308', 'nombre' => 'QUILACO', 'provincia' => 'BIOBIO'],
            ['codigo' => '309', 'nombre' => 'QUILLECO', 'provincia' => 'BIOBIO'],
            ['codigo' => '310', 'nombre' => 'SAN ROSENDO', 'provincia' => 'BIOBIO'],
            ['codigo' => '311', 'nombre' => 'SANTA BARBARA', 'provincia' => 'BIOBIO'],
            ['codigo' => '312', 'nombre' => 'TUCAPEL', 'provincia' => 'BIOBIO'],
            ['codigo' => '313', 'nombre' => 'YUMBEL', 'provincia' => 'BIOBIO'],
            ['codigo' => '314', 'nombre' => 'ALTO BIOBIO', 'provincia' => 'BIOBIO'],

            // Provincia de Ñuble
            ['codigo' => '401', 'nombre' => 'CHILLAN', 'provincia' => 'NUBLE'],
            ['codigo' => '402', 'nombre' => 'BULNES', 'provincia' => 'NUBLE'],
            ['codigo' => '403', 'nombre' => 'COBQUECURA', 'provincia' => 'NUBLE'],
            ['codigo' => '404', 'nombre' => 'COELEMU', 'provincia' => 'NUBLE'],
            ['codigo' => '405', 'nombre' => 'COIHUECO', 'provincia' => 'NUBLE'],
            ['codigo' => '406', 'nombre' => 'CHILLAN VIEJO', 'provincia' => 'NUBLE'],
            ['codigo' => '407', 'nombre' => 'EL CARMEN', 'provincia' => 'NUBLE'],
            ['codigo' => '408', 'nombre' => 'NINHUE', 'provincia' => 'NUBLE'],
            ['codigo' => '409', 'nombre' => 'NIQUEN', 'provincia' => 'NUBLE'],
            ['codigo' => '410', 'nombre' => 'PEMUCO', 'provincia' => 'NUBLE'],
            ['codigo' => '411', 'nombre' => 'PINTO', 'provincia' => 'NUBLE'],
            ['codigo' => '412', 'nombre' => 'PORTEZUELO', 'provincia' => 'NUBLE'],
            ['codigo' => '413', 'nombre' => 'QUILLON', 'provincia' => 'NUBLE'],
            ['codigo' => '414', 'nombre' => 'QUIRIHUE', 'provincia' => 'NUBLE'],
            ['codigo' => '415', 'nombre' => 'RANQUIL', 'provincia' => 'NUBLE'],
            ['codigo' => '416', 'nombre' => 'SAN CARLOS', 'provincia' => 'NUBLE'],
            ['codigo' => '417', 'nombre' => 'SAN FABIAN', 'provincia' => 'NUBLE'],
            ['codigo' => '418', 'nombre' => 'SAN IGNACIO', 'provincia' => 'NUBLE'],
            ['codigo' => '419', 'nombre' => 'SAN NICOLAS', 'provincia' => 'NUBLE'],
            ['codigo' => '420', 'nombre' => 'TREGUACO', 'provincia' => 'NUBLE'],
            ['codigo' => '421', 'nombre' => 'YUNGAY', 'provincia' => 'NUBLE'],
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