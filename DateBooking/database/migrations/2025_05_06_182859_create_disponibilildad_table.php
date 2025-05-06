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

        Schema::create('disponibilildad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_servicio');
            $table->date('fecha')->nullable();
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->time('intevalo');
            $table->enum('dias', ["lunes","martes","miercoles","jueves","viernes","sabado","domingo"]);
            $table->string('tipo', 20);
            $table->tinyInteger('activo');
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
