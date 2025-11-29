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
        Schema::table('planos', function (Blueprint $table) {
            // 1. Eliminar columna "providencia" (ya no se usa)
            $table->dropColumn('providencia');

            // 2. Renombrar columna "archivo" a "providencia_archivo"
            $table->renameColumn('archivo', 'providencia_archivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            // Revertir: renombrar de vuelta
            $table->renameColumn('providencia_archivo', 'archivo');

            // Restaurar columna "providencia"
            $table->string('providencia')->nullable()->after('proyecto');
        });
    }
};
