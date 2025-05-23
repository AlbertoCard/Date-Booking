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
        Schema::disableForeignKeyConstraints();

        Schema::create('reseñas', function (Blueprint $table) {
            $table->bigIncrements('id_reseña')->primary();
            $table->string('id_usuario', 128);
            $table->foreign('id_usuario')->references('uid')->on('usuarios');
            $table->unsignedBigInteger('id_servicio');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios');
            $table->char('calificacion', 1);
            $table->string('descripcion')->default('');
            $table->date('fecha')->default(now());
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseñas');
    }
};
