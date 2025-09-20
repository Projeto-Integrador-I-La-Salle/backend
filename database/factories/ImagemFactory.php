<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImagemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_produto' => Produto::inRandomOrder()->first()->id_produto ?? Produto::factory(),
            'url_imagem' => 'https://picsum.photos/seed/' . $this->faker->unique()->word() . '/600/600',
        ];
    }
}
