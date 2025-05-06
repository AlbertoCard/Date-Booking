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

        Schema::create('medicos', function (Blueprint $table) {
            $table->bigIncrements('id_medico')->primary();
            $table->unsignedBigInteger('id_servicio');
            $table->foreign('id_servicio')->references('id_servicio')->on('servicios');
            $table->string('nombre');
            $table->string('especialidad');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
