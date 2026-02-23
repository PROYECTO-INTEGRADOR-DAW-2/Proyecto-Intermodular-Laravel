<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::updateOrCreate(
            ['nombre' => 'Zapatillas nike gato'],
            [
                'sku' => '99000',
                'marca' => "Nike",
                'categoria' => "Zapatillas",
                'precio' => 100.50,
                'talla' => "34",
                'color' => "Blanco",
                'stock' => 12,
                'ajuste' => "Ajustado",
                'sexo' => "Hombre",
                'descripcion' => "Zapatillas nike",
                'altura' => "Normal",
                'deporte' => "Trail",
                'oferta' => false,
                'img' => ""
            ]
        );

        Product::factory()->count(100)->create();

    }
}
