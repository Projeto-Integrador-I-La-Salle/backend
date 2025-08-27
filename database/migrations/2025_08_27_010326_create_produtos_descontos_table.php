<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('produtos_descontos', function (Blueprint $table) {
            $table->foreignId('id_produto')->constrained('produtos', 'id_produto');
            $table->foreignId('id_desconto')->constrained('descontos', 'id_descontos');
            $table->primary(['id_produto', 'id_desconto']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('produtos_descontos');
    }
};