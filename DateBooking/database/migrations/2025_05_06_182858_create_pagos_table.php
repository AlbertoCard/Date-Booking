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

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreign('id_pago')->references('id_pago')->on('reservas');
            $table->string('id_usuario', 128);
            $table->foreign('id_usuario')->references('uid')->on('usuarios');
            $table->string('stripe_payment_intent_id');
            $table->string('stripe_customer_id');
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 10);
            $table->string('metodo_pago', 100);
            $table->string('estado_pago', 50);
            $table->dateTime('fecha_pago');
            $table->timestamp('creacion');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
