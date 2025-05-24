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

        Schema::create('reservas', function (Blueprint $table) {
            $table->bigIncrements('id_reserva')->primary();
            $table->string('id_usuario', 128);
            $table->foreign('id_usuario')->references('uid')->on('usuarios');
            $table->unsignedBigInteger('id_servicio');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios');
            $table->string('estado', 50);
            $table->dateTime('fecha');
            $table->string('tipo_servicio', 50);
            $table->string('detalle_1');
            $table->string('detalle_2');
            $table->timestamp('disponible_hasta');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
