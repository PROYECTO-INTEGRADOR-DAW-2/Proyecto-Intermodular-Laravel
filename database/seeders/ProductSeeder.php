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
        Product::firstOrCreate(
            ['nom' => 'Zapatillas nike gato'],
            ['marca' => "Nike"],
            ['categoria' => "Zapatillas"],
            ['nombre' => "99000"],
            ['precio' => 100.50],
            ['talla' => "34"],
            ['color' => "Blanco"],
            ['stock' => 12],
            ['ajuste' => "Ajustado"],
            ['sexo' => "Hombre"],
            ['descripcion' => "Zapatillas nike"],
            ['altura' => "Normal"],
            ['deporte' => "Trail"],
            ['oferta' => false],
            ['img' => ""]

        );

    }
}
