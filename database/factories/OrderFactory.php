<?php

namespace Database\Factories;

use App\Models\ORDER;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class OrderFactory extends Factory
{

    protected $model = Order::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => $this->faker->bothify('ORD-####-????')
            'user_id' => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
            'total_amount' => fake()->randomNumber(3),
            'tax_amount' => fake()->randomElement(["Camisetas", "Pantalones", "Zapatillas"]),
            'nombre' => fake()->unique()->words(3, true),
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
            'img' => fake()->imageUrl(),
        ];
    }
}
