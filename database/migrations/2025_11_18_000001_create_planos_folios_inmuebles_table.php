<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabla para guardar el detalle de cada hijuela/sitio individual
     * Un folio puede tener múltiples hijuelas o sitios
     */
    public function up(): void
    {
        Schema::create('planos_folios_inmuebles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plano_folio_id')->constrained('planos_folios')->onDelete('cascade');
            $table->integer('numero_inmueble'); // 1, 2, 3...
            $table->enum('tipo_inmueble', ['HIJUELA', 'SITIO']);
            $table->decimal('hectareas', 10, 4)->nullable(); // Solo para HIJUELA
            $table->bigInteger('m2'); // Metros cuadrados
            $table->timestamps();

            // Índice para búsquedas
            $table->index(['plano_folio_id', 'numero_inmueble']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos_folios_inmuebles');
    }
};
