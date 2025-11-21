<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- REGISTRO ----------
        User::updateOrCreate(
            ['email' => 'lperez@mbienes.cl'],
            [
                'name' => 'Loreto Pérez',
                'password' => Hash::make('loreto2025'),
                'role' => 'registro',
            ]
        );

        User::updateOrCreate(
            ['email' => 'anorambuena@mbienes.cl'],
            [
                'name' => 'Alfonso Norambuena',
                'password' => Hash::make('alfonso2025'),
                'role' => 'registro',
            ]
        );

        User::updateOrCreate(
            ['email' => 'jcarvajal@mbienes.cl'],
            [
                'name' => 'Julio Carvajal',
                'password' => Hash::make('jcarvajal2025'),
                'role' => 'registro',
            ]
        );

        User::updateOrCreate(
            ['email' => 'cmartinezc@mbienes.cl'],
            [
                'name' => 'Carlos Martínez',
                'password' => Hash::make('carlos2025'),
                'role' => 'registro',
            ]
        );

        User::updateOrCreate(
            ['email' => 'jhidalgo@mbienes.cl'],
            [
                'name' => 'José Hidalgo',
                'password' => Hash::make('jose2025'),
                'role' => 'registro',
            ]
        );

        User::updateOrCreate(
            ['email' => 'vsepulveda@mbienes.cl'],
            [
                'name' => 'Verónica Pérez',
                'password' => Hash::make('vero2025'),
                'role' => 'registro',
            ]
        );

        User::updateOrCreate(
            ['email' => 'fsanmartin@mbienes.cl'],
            [
                'name' => 'Felipe San Martín',
                'password' => Hash::make('felipe2025'),
                'role' => 'registro',
            ]
        );

        // ---------- CONSULTA ----------
        User::updateOrCreate(
            ['email' => 'mmoncada@mbienes.cl'],
            [
                'name' => 'Matías Moncada',
                'password' => Hash::make('matias2025'),
                'role' => 'consulta',
            ]
        );
    }
}
