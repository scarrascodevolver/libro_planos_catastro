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
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_plano', 13)->unique(); // 30329271SU
            $table->char('codigo_region', 2)->default('08');
            $table->char('codigo_comuna', 3)->notNull();
            $table->integer('numero_correlativo')->notNull();
            $table->enum('tipo_saneamiento', ['SR', 'SU', 'CR', 'CU']);
            $table->string('provincia', 100);
            $table->string('comuna', 100);
            $table->string('mes', 20);
            $table->integer('ano');
            $table->string('responsable', 255);
            $table->string('proyecto', 255); // Acepta "CONVENIO-FINANCIAMIENTO" o cualquier otro nombre
            $table->decimal('total_hectareas', 10, 4)->nullable();
            $table->bigInteger('total_m2');
            $table->integer('cantidad_folios')->default(0);
            $table->text('observaciones')->nullable();
            $table->string('archivo', 255)->nullable();
            $table->string('tubo', 255)->nullable();
            $table->string('tela', 255)->nullable();
            $table->string('archivo_digital', 255)->nullable();
            $table->unsignedBigInteger('created_by'); // FK a users
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');

            // Índices para optimizar consultas
            $table->index('codigo_comuna');
            $table->index('tipo_saneamiento');
            $table->index(['mes', 'ano']);
            $table->index('responsable');
            $table->index('proyecto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};