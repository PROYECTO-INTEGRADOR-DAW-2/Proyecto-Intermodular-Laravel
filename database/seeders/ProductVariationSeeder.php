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
        $modelCounter = 0;

        foreach ($productos as $producto) {

            //Segun la categoria y sexo del producto seleccionamos las tallas que pertenezcan a la categoria
            $tallasDisponibles = Talla::where([
                ['categoria', '=', $this->getTallaCategory($producto->sexo, $producto->categoria)],
                ['genero', '=', $producto->sexo]
            ])->distinct()->get();

            if ($tallasDisponibles->isEmpty()) {
                continue;
            }

            //Cantidad random de tallas para cada producto
            $tallasACrear = rand(1, count($tallasDisponibles));

            //Creamos unas cuantas tallas para cada producto
            for ($i = 0; $i < $tallasACrear; $i++) {
                $tallaSeleccionada = $tallasDisponibles[$i];

                //Para cada talla creamos todos los colores disponibles en la BBDD
                foreach($colores as $color) {

                    //Generacion de SKU
                    $numeroModelo = str_pad($modelCounter, 4, '0', STR_PAD_LEFT);
                    $prefijoCategoria = strtoupper(substr($producto->categoria, 0, 3));
                    $colorCodigo = $color->prefijo;
                    $nombreTalla = $tallaSeleccionada->nombre;
        
                    $sku = "{$prefijoCategoria}-{$numeroModelo}-{$colorCodigo}-{$nombreTalla}";

                    Variation::create([
                        'product_id' => $producto->id,
                        'sku' => $sku,
                        'color_id' => $color->id,
                        'size_id' => $tallaSeleccionada->id,
                        'stock' => rand(1, 50)
                    ]);
                };

            }

            $modelCounter++;

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
