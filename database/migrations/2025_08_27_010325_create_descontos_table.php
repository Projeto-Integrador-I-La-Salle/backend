<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('descontos', function (Blueprint $table) {
            $table->id('id_descontos');
            $table->string('nome');
            $table->timestamp('data_inicio')->nullable();
            $table->timestamp('data_fim')->nullable();
            $table->decimal('porcentagem', 5, 2)->nullable(); // Corrigido de (2,2) para (5,2)
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('descontos');
    }
};