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
            // Hacer columnas nullable para permitir "al menos uno de los dos"
            // Hectáreas ya es nullable por defecto, pero lo hacemos explícito
            $table->decimal('hectareas', 10, 4)->nullable()->change();
            // M² ahora puede ser NULL (antes era NOT NULL)
            $table->decimal('m2', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planos_folios', function (Blueprint $table) {
            // Revertir a NOT NULL (valores por defecto si es necesario)
            $table->decimal('hectareas', 10, 4)->nullable()->change();
            $table->decimal('m2', 15, 2)->nullable(false)->default(0)->change();
        });
    }
};
