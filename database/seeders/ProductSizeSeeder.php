<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductoTalla;
use App\Models\Talla;


class ProductSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       $productos = Product::all(['id', 'categoria', 'sexo']);
       
       foreach($productos as $producto) {

            //Segun la categoria y sexo del producto seleccionamos las tallas que pertenezcan a la categoria 

            $tallasDisponibles = Talla::where([
                ['categoria', '=', $producto->categoria],
                ['genero', '=', $this->getGenderColumnTallaValue($producto->sexo, $producto->categoria)]
            ]);

            $cantidadAEnlazar = rand(1,4);

            for ($i = 0; $i<=$cantidadAEnlazar; $i++) {
                $tallaSeleccionada = $tallasDisponibles[rand(1, count($tallasDisponibles))];

                ProductoTalla::create([
                    'product_id' => $producto->id,
                    'talla_id' => $tallaSeleccionada
                ]);


            }

       }



    }

    /**
     * Obtiene el nombre de la columna a buscar segun el genero del producto
     */
    public function getGenderColumnTallaValue(string $productGender, string $productCategory) {

        $prendasCategories = ['Pantalones', 'Camisetas', 'Calcetines'];
        

        switch ($productGender) {
            case 'Mujer':
            case 'Hombre':
                if (in_array($productCategory, $prendasCategories)) {
                    return 'Prendas Adultos';
                } else {
                    return 'Zapatillas Adultos';
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
