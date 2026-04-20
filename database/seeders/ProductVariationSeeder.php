<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Talla;
use App\Models\Color;
use App\Models\Variation;

class ProductVariationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $productos = Product::all(['id', 'categoria', 'sexo']);
        $colores = Color::all();

        foreach ($productos as $producto) {

            //Segun la categoria y sexo del producto seleccionamos las tallas que pertenezcan a la categoria 

            $tallasDisponibles = Talla::where([
                ['categoria', '=', $this->getTallaCategory($producto->sexo, $producto->categoria)],
                ['genero', '=', $producto->sexo]
            ])->distinct()->get();

            if ($tallasDisponibles->isEmpty()) {
                continue;
            }

            $tallasACrear = rand(1, count($tallasDisponibles));

            for ($i = 0; $i < $tallasACrear; $i++) {
                $tallaSeleccionada = $tallasDisponibles[$i];

                foreach($colores as $color) {
                    Variation::create([
                        'product_id' => $producto->id,
                        'color_id' => $color->id,
                        'size_id' => $tallaSeleccionada->id,
                        'stock' => rand(1, 50)
                    ]);
                };

            }

        }



    }

    /**
     * Obtiene el nombre de la columna a buscar segun el genero del producto y la categoria del producto
     */
    public function getTallaCategory(string $productGender, string $productCategory)
    {

        $prendasCategories = ['Pantalones', 'Camisetas', 'Calcetines'];


        switch ($productGender) {
            case 'Mujer':
            case 'Hombre':
                if (in_array($productCategory, $prendasCategories)) {
                    return 'Prendas Adulto';
                } else {
                    return 'Zapatillas Adulto';
                }
                break;
            case 'Niño':
            case 'Niña':
                if (in_array($productCategory, $prendasCategories)) {
                    return 'Prendas Infantil';
                } else {
                    return 'Zapatillas Infantil';
                }

        }
    }
}
