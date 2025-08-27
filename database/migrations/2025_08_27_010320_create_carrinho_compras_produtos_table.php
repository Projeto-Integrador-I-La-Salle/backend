<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carrinho_compras_produtos', function (Blueprint $table) {
            $table->foreignId('id_carrinho_compras')->constrained('carrinho_compras', 'id_carrinho_compras')->onDelete('cascade');
            $table->foreignId('id_produto')->constrained('produtos', 'id_produto');
            $table->primary(['id_carrinho_compras', 'id_produto']); // Chave primária composta
        });
    }
    public function down(): void {
        Schema::dropIfExists('carrinho_compras_produtos');
    }
};