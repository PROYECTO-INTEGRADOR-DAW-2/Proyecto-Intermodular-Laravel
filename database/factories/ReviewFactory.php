<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ReviewFactory extends Factory
{

    protected $model = Review::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::query()->inRandomOrder()->first()?->id ?? Product::factory(),
            'user_id' => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
            'valoracion' => fake()->randomFloat(1,0,5),
            'comentario' => fake()->sentence()
        ];
    }
}
