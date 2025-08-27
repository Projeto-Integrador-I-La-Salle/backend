<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categorias_especiais_produtos', function (Blueprint $table) {
            $table->foreignId('id_categorias_especiais')->constrained('categorias_especiais', 'id_categorias_especiais')->onDelete('cascade');
            $table->foreignId('id_produto')->constrained('produtos', 'id_produto');
            $table->primary(['id_categorias_especiais', 'id_produto']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('categorias_especiais_produtos');
    }
};