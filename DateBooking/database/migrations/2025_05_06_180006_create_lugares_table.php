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

        Schema::create('lugares', function (Blueprint $table) {
            $table->id('id_lugar');
            $table->foreignId('id_servicio')->references('id_servicio')->on('servicios');
            $table->string('fila');
            $table->integer('numero');
            $table->string('sector');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lugares');
    }
};
