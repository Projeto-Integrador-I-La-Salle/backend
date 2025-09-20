<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement([
                'Motos Novas',
                'Motos Usadas',
                'Peças e Acessórios',
                'Capacetes e Equipamentos',
                'Roupas e Vestuário',
                'Manutenção e Ferramentas',
                'Eletrônicos para Motos',
            ]),
        ];
    }
}
