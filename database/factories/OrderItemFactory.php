<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class OrderItemFactory extends Factory
{

    protected $model = OrderItem::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::query()->inRandomOrder()->first()?->id ?? Order::factory(),
            
            // Relación con usuario
            'product_id' => Product::query()->inRandomOrder()->first()?->id ?? Product::factory(),

            'quantity' => $this->faker->numberBetween(1, 5),

            'price' => fn (array $attributes) => Product::find($attributes['product_id'])->precio,

        ];
    }
}
