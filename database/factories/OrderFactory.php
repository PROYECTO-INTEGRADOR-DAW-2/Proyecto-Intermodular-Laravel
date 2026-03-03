<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\OrderStatusEnum;

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
            'order_number' => $this->faker->bothify('ORD-####-????'),
            
            // Relación con usuario
            'user_id' => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
            
            // Status usando el Enum (quitamos los paréntesis de faker)
            'status' => $this->faker->randomElement([
                OrderStatus::CANCELLED, 
                OrderStatus::DELIVERED, 
                OrderStatus::PAID, 
                OrderStatus::SHIPPED, 
                OrderStatus::PENDING
            ]),
            
            // Precios (usamos randomFloat para decimales reales, tt)
            'subtotal' => $this->faker->randomFloat(2, 50, 500),
            'tax_amount' => $this->faker->randomFloat(2, 5, 50),
            'shipping_amount' => $this->faker->randomFloat(2, 0, 15),

            
            'payment_method' => $this->faker->randomElement(["Visa", "Transfer", "Paypal"]),
            'shipping_address' => $this->faker->address()
        ];
    }
}
