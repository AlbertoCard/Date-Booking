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

        Schema::create('servicios', function (Blueprint $table) {
            $table->bigIncrements('id_servicio')->primary();
            $table->unsignedBigInteger('id_establecimiento');
            $table->foreign('id_establecimiento')->references('id_establecimiento')->on('establecimientos');
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('costo', 10, 2);
            $table->string('categoria');
            $table->unsignedInteger('id_ciudad');
            $table->foreign('id_ciudad')->references('id_ciudad')->on('ciudades');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
