<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = Product::all();

        Order::factory(50)->create()->each(function ($order) use ($productos) {
        // 3. Para cada pedido, le metemos entre 1 y 5 productos aleatorios
        $items = OrderItem::factory(rand(1, 5))->create([
            'order_id' => $order->id,
            'product_id' => $productos->random()->id
        ]);

        // 4. Sincronizamos el total_amount del pedido con la suma de sus items
        $order->update([
            'subtotal' => $items->sum(fn($item) => $item->price * $item->quantity)
        ]);


    });


        

       
    }
}
