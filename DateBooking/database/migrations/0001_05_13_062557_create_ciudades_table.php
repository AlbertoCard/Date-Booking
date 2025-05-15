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
        //
        Schema::create('ciudades', function (Blueprint $table) {
            $table->integerIncrements('id_ciudad')->primary();
            $table->string('nombre');
            $table->unsignedInteger('id_estado');
            $table->foreign('id_estado')->references('id_estado')->on('estados');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
