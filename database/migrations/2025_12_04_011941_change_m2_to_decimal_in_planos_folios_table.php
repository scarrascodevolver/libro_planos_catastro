<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('planos_folios', function (Blueprint $table) {
            // Cambiar m2 de bigInteger a decimal(15,2)
            // 15 dígitos totales, 2 decimales
            // Permite valores como: 9999999999999,99
            $table->decimal('m2', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planos_folios', function (Blueprint $table) {
            // Revertir a bigInteger (sin decimales)
            $table->bigInteger('m2')->change();
        });
    }
};
