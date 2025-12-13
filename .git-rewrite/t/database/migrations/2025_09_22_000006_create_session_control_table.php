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
        Schema::create('session_control', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->notNull();
            $table->string('session_id', 255)->notNull();
            $table->boolean('has_control')->default(false);
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');

            // Índices para control de sesiones
            $table->index(['has_control', 'is_active']);
            $table->index(['user_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_control');
    }
};