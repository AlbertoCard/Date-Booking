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
            $table->string('id_usuario', 128);
            $table->foreign('id_usuario')->references('uid')->on('usuarios');
            $table->unsignedBigInteger('id_establecimiento');
            $table->foreign('id_establecimiento')->references('id_establecimiento')->on('establecimientos');
            $table->timestamps();
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
