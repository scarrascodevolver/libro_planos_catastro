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
        Schema::create('planos_folios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plano_id')->notNull();
            $table->string('folio', 50)->nullable(); // NO unique, puede repetirse en diferentes planos, NULL para fiscales
            $table->string('solicitante', 255);
            $table->string('apellido_paterno', 255)->nullable();
            $table->string('apellido_materno', 255)->nullable();
            $table->enum('tipo_inmueble', ['HIJUELA', 'SITIO']);
            $table->integer('numero_inmueble')->nullable();
            $table->decimal('hectareas', 10, 4)->nullable(); // Solo para HIJUELA (NULL para sitios)
            $table->bigInteger('m2'); // Para ambos tipos
            $table->boolean('is_from_matrix')->default(true); // true=Matrix, false=Manual
            $table->string('matrix_folio', 50)->nullable(); // Referencia original Matrix
            $table->timestamps();

            $table->foreign('plano_id')->references('id')->on('planos')->onDelete('cascade');

            // Índices para optimizar consultas
            $table->index('folio');
            $table->index('solicitante');
            $table->index(['apellido_paterno', 'apellido_materno']);
            $table->index('plano_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos_folios');
    }
};