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
        Schema::create('comunas_biobio', function (Blueprint $table) {
            $table->char('codigo', 3)->primary(); // 101, 102, 301, 201, 401
            $table->string('nombre', 100)->notNull();
            $table->string('provincia', 100)->notNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comunas_biobio');
    }
};