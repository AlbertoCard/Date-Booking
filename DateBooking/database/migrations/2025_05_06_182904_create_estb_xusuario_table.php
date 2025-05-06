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

        Schema::create('estb_xusuario', function (Blueprint $table) {
            $table->string('id_usuario', 128)->primary();
            $table->foreign('id_usuario')->references('uid')->on('usuarios');
            $table->bigInteger('id_establecimiento');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estb_xusuario');
    }
};
