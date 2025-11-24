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
        Schema::table('session_control', function (Blueprint $table) {
            // Eliminar la FK anterior sin cascade
            $table->dropForeign(['user_id']);

            // Recrear la FK con onDelete cascade
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_control', function (Blueprint $table) {
            // Eliminar la FK con cascade
            $table->dropForeign(['user_id']);

            // Restaurar la FK sin cascade (estado original)
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');
        });
    }
};
