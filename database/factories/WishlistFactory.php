<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\WishlistItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class WishlistFactory extends Factory
{

    protected $model = WishlistItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
            'product_id' => Product::query()->inRandomOrder()->first()?->id ?? Product::factory()
        ];
    }
}
