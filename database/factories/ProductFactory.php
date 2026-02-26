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
            'marca' => fake()->randomElement(["Adidas", "Nike", "Asics","Puma"]),
            'categoria' => fake()->randomElement(["Camisetas", "Pantalones", "Zapatillas"]),
            'nombre' => fake()->word(),
            'precio' => fake()->randomFloat(2, 1, 100),
            'talla' => fake()->word(),
            'color' => fake()->word(),
            'stock' => fake()->randomNumber(2),
            'ajuste' => fake()->randomElement(["Ajustado", "Holgado", "Normal"]),
            'sexo' => fake()->randomElement(["Hombre", "Mujer", "Niño"]),
            'descripcion' => fake()->sentence(),
            'altura' => fake()->randomElement(["Bajo", "Alto", "Normal"]),
            'deporte' => fake()->randomElement(["Trail"]),
            'oferta' => fake()->boolean(),
            'is_eco' => fake()->boolean(30),
            'img' => fake()->imageUrl(),
        ];
    }
}
