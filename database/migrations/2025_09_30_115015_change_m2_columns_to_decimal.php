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
        // Cambiar m2 de BIGINT a DECIMAL(15,2) en planos_folios
        Schema::table('planos_folios', function (Blueprint $table) {
            $table->decimal('m2', 15, 2)->change();
        });

        // Cambiar total_m2 de BIGINT a DECIMAL(15,2) en planos
        Schema::table('planos', function (Blueprint $table) {
            $table->decimal('total_m2', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir a BIGINT si es necesario
        Schema::table('planos_folios', function (Blueprint $table) {
            $table->bigInteger('m2')->change();
        });

        Schema::table('planos', function (Blueprint $table) {
            $table->bigInteger('total_m2')->change();
        });
    }
};
