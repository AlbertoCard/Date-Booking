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

        Schema::create('establecimientos', function (Blueprint $table) {
            $table->bigIncrements('id_establecimiento');
            $table->string('nombre');
            $table->string('telefono', 20)->default('0000000000');
            $table->string('direccion')->default('Sin dirección');
            $table->string('rfc', 50)->default('Sin RFC');
            $table->string('estado', 10)->default('Sin estado');
            $table->string('codigo_postal', 10)->default('00000');
            $table->string('pais', 100)->default('Sin país');
            $table->unsignedInteger('id_estado')->default(0);
            $table->string('stripe_account_id')->default('Sin cuenta');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};
