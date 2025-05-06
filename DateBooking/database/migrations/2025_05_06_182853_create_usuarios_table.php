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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->string('uid', 128)->primary();
            $table->string('nombre');
            $table->string('email');
            $table->string('telefono', 20)->default('0000000000');
            $table->text('foto_url');
            $table->string('rol', 20)->default('cliente');
            $table->dateTime('fecha_creacion')->default(now());
            $table->tinyInteger('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
