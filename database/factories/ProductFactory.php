<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    protected $model = Product::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => fake()->unique()->randomNumber(8),
            'marca' => fake()->word(),
            'categoria' => fake()->word(),
            'nombre' => fake()->word(),
            'precio' => fake()->randomFloat(2, 1, 100),
            'talla' => fake()->word(),
            'color' => fake()->word(),
            'stock' => fake()->randomNumber(2),
            'ajuste' => fake()->randomNumber(2),
            'sexo' => fake()->word(),
            'descripcion' => fake()->sentence(),
            'altura' => fake()->randomNumber(2),
            'deporte' => fake()->word(),
            'oferta' => fake()->randomNumber(2),
            'img' => fake()->imageUrl(),
        ];
    }
}
