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
            'sku' => $this->faker->unique()->randomNumber(8),
            'marca' => $this->faker->randomElement(["Adidas", "Nike", "Asics","Puma"]),
            'categoria' => $this->faker->randomElement(["Camisetas", "Pantalones", "Zapatillas"]),
            'nombre' => $this->faker->word(),
            'precio' => $this->faker->randomFloat(2, 1, 100),
            'talla' => $this->faker->word(),
            'color' => $this->faker->word(),
            'stock' => $this->faker->randomNumber(2),
            'ajuste' => $this->faker->randomElement(["Ajustado", "Holgado", "Normal"]),
            'sexo' => $this->faker->randomElement(["Hombre", "Mujer", "Niño"]),
            'descripcion' => $this->faker->sentence(),
            'altura' => $this->faker->randomElement(["Bajo", "Alto", "Normal"]),
            'deporte' => $this->faker->randomElement(["Trail"]),
            'oferta' => $this->faker->boolean(),
            'img' => $this->faker->imageUrl(),
        ];
    }
}
