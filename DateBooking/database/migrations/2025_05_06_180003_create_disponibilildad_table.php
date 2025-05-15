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

        Schema::create('disponibilidad', function (Blueprint $table) {
            $table->bigIncrements('id_disponibilidad')->primary();
            $table->unsignedBigInteger('id_servicio');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios');
            $table->date('fecha')->nullable();
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->time('intervalo');
            $table->enum('dias', ["lunes","martes","miercoles","jueves","viernes","sabado","domingo"]);
            $table->string('tipo', 20);
            $table->tinyInteger('activo')->default(1);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilildad');
    }
};
