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
        Schema::create('reserva_itens', function (Blueprint $table) {
            $table->id('id_item_reserva');
            $table->foreignId('id_reserva')->constrained('reservas', 'id_reserva')->onDelete('cascade');
            $table->foreignId('id_produto')->constrained('produtos', 'id_produto');
            $table->integer('qtd_reservada');
            // Não adicionamos o preço unitário, conforme decisão da equipe.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva_itens');
    }
};
