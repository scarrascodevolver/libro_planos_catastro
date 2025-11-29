<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Alfonso Norambuena',
                'email' => 'alfonso.norambuena@biobio.cl',
                'email_verified_at' => now(),
                'role' => 'registro', // Rol con permisos completos
                'password' => Hash::make('alfonso123'),
                'initial_password' => 'alfonso123',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Usuario Consulta',
                'email' => 'consulta@biobio.cl',
                'email_verified_at' => now(),
                'role' => 'consulta', // Solo lectura
                'password' => Hash::make('consulta123'),
                'initial_password' => 'consulta123',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}