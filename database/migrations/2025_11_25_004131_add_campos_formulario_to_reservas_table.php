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
    Schema::table('reservas', function (Blueprint $table) {
        // Campos novos baseados no formulário
        $table->string('telefone_contato')->after('id_usuario'); // Contato
        $table->date('data_retirada')->after('status');         // Data para retirada
        $table->string('metodo_pagamento')->after('data_retirada'); // Dinheiro, Pix, etc.
        $table->text('observacao')->nullable()->after('metodo_pagamento'); // Anotações (Opcional)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            //
        });
    }
};
