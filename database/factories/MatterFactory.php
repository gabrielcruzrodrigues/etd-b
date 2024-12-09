<?php

namespace Database\Factories;

use App\Models\Matter\Matter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Matter\Matter>
 */
class MatterFactory extends Factory
{
    /**
     * O nome do modelo correspondente a esta fábrica.
     *
     * @var string
     */
    protected $model = Matter::class;

    /**
     * Define o estado padrão do modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word
        ];
    }
}
