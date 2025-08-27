<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lista_desejos_produtos', function (Blueprint $table) {
            $table->foreignId('id_lista_desejos')->constrained('lista_desejos', 'id_lista_desejos')->onDelete('cascade');
            $table->foreignId('id_produto')->constrained('produtos', 'id_produto');
            $table->primary(['id_lista_desejos', 'id_produto']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('lista_desejos_produtos');
    }
};