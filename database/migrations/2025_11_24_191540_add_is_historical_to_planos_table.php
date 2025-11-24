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
            // Agregar campo para identificar planos históricos importados
            $table->boolean('is_historical')->default(false)->after('id');
            $table->index('is_historical'); // Índice para consultas de eliminación
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            // Eliminar índice y campo
            $table->dropIndex(['is_historical']);
            $table->dropColumn('is_historical');
        });
    }
};
