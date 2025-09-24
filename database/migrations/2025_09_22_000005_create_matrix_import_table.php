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
        Schema::create('matrix_import', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50); // NO unique, puede repetirse
            $table->string('tipo_inmueble', 100);
            $table->string('provincia', 100);
            $table->string('comuna', 100);
            $table->string('nombres', 255);
            $table->string('apellido_paterno', 255)->nullable();
            $table->string('apellido_materno', 255)->nullable();
            $table->string('responsable', 255);
            $table->string('convenio_financiamiento', 255); // Nombre original del campo Matrix
            $table->string('batch_import', 50); // MATRIX-2025-09
            $table->timestamps();

            // Índices para optimizar búsquedas de autocompletado
            $table->index('folio');
            $table->index('batch_import');
            $table->index(['nombres', 'apellido_paterno', 'apellido_materno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matrix_import');
    }
};