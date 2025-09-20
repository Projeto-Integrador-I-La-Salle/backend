<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Imagem;
use App\Models\Produto;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Categoria::factory(10)->create();

        Produto::factory(50)->create()->each(function ($produto) {
            Imagem::factory(rand(1, 3))->create([
                'id_produto' => $produto->id_produto,
            ]);
        });
    }
}
