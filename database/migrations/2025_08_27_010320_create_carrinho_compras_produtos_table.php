<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('carrinho_compras_produtos', function (Blueprint $table) {
            $table->id(); // <-- ADICIONADO: Cria uma PK auto-incremental 'id'
            $table->foreignId('id_carrinho_compras')->constrained('carrinho_compras', 'id_carrinho_compras')->onDelete('cascade');
            $table->foreignId('id_produto')->constrained('produtos', 'id_produto');
            $table->integer('quantidade')->default(1);
            // A linha abaixo foi removida, pois $table->id() já é a chave primária
            // $table->primary(['id_carrinho_compras', 'id_produto']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('carrinho_compras_produtos');
    }
};