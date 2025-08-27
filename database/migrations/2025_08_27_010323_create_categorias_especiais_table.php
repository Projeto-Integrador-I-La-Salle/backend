<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categorias_especiais', function (Blueprint $table) {
            $table->id('id_categorias_especiais');
            $table->string('nome')->unique();
            $table->timestamp('data_inicio');
            $table->timestamp('data_fim');
            $table->decimal('porcentagem', 5, 2)->nullable(); // Corrigido de (2,2) para (5,2)
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('categorias_especiais');
    }
};