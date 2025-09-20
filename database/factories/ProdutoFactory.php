<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_publico' => $this->faker->uuid(),
            'id_categoria' => Categoria::inRandomOrder()->first()->id_categoria ?? Categoria::factory(),
            'nome' => $this->faker->words(3, true),
            'descricao' => $this->faker->sentence(15),
            'preco' => $this->faker->randomFloat(2, 10, 5000),
            'qtd_estoque' => $this->faker->numberBetween(0, 500),
        ];
    }
}
