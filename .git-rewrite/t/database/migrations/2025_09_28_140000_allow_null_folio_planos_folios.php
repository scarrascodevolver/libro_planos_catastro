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
            // Permitir NULL en folio para planos fiscales
            $table->string('folio', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planos_folios', function (Blueprint $table) {
            // Revertir a NOT NULL (solo si no hay registros con folio NULL)
            $table->string('folio', 50)->nullable(false)->change();
        });
    }
};