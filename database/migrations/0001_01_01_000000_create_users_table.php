<?php
// Arquivo: ...0001_01_01_000000_create_users_table.php

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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Laravel usa 'id' por padrão. Vamos manter.
            $table->uuid('id_publico')->unique(); // Nosso identificador público
            $table->string('name'); // Laravel usa 'name'. Vamos manter.
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Laravel usa 'password'. Vamos manter.
            $table->string('telefone')->nullable(); // Nossa coluna
            $table->enum('permissao', ['cliente', 'admin'])->default('cliente'); // Nossa coluna
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};