<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\CategoriaTalla;
use App\Models\Talla;


class TallasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listaCategoriasTallas = CategoriaTalla::cases();
        $listaTallasNumericas = [
            'INFANTIL' => range(20,35),
            'ADULTO' => range(36, 47)
        ];

        $listaTallasPrendas = [
            'INFANTIL' => [
                '3-4 años (104 cm)',
                '4-5 años (110 cm)',
                '5-6 años (116 cm)',
                '6-7 años (122 cm)',
                '7-8 años (128 cm)',
                '8-9 años (134 cm)',
                '9-10 años (140 cm)',
                '10-11 años (146 cm)',
                '11-12 años (152 cm)',
                '12-13 años (158 cm)',
                '13-14 años (164 cm)',
                '14-15 años (170 cm)',
                '15-16 años (176 cm)',
            ],
            'ADULTO' => ['XS', 'S', 'M', 'L', 'XL']
        ];

        foreach ($listaCategoriasTallas as $segmento) {

            switch ($segmento->name) {
                case 'INFANTIL':

                    foreach ($listaTallasNumericas[$segmento->name] as $tallaNumerica) {
                        
                        Talla::create([
                            'nombre' => $tallaNumerica,
                            'segmento' => $segmento->name,
                            'genero' => 'Niño',
                            'categoria' => 'Zapatillas Infantil'
                        ]);

                        Talla::create([
                            'nombre' => $tallaNumerica,
                            'segmento' => $segmento->name,
                            'genero' => 'Niña',
                            'categoria' => 'Zapatillas Infantil'
                        ]);
                        
                    }

                    foreach ($listaTallasPrendas[$segmento->name] as $tallaPrenda) {
                        
                        Talla::create([
                            'nombre' => $tallaPrenda,
                            'segmento' => $segmento->name,
                            'genero' => 'Niño',
                            'categoria' => 'Prendas Infantil'
                        ]);

                        Talla::create([
                            'nombre' => $tallaPrenda,
                            'segmento' => $segmento->name,
                            'genero' => 'Niña',
                            'categoria' => 'Prendas Infantil'
                        ]);
                        
                    }
                    
                    break;

                case 'ADULTO':

                    foreach ($listaTallasNumericas[$segmento->name] as $tallaNumerica) {
                        
                        
                        Talla::create([
                            'nombre' => $tallaNumerica,
                            'segmento' => $segmento->name,
                            'genero' => 'Mujer',
                            'categoria' => 'Zapatillas Adulto'
                        ]);

                        Talla::create([
                            'nombre' => $tallaNumerica,
                            'segmento' => $segmento->name,
                            'genero' => 'Hombre',
                            'categoria' => 'Zapatillas Adulto'
                        ]);
                        
                    }

                    foreach ($listaTallasPrendas[$segmento->name] as $tallaPrenda) {
                        
                        
                        Talla::create([
                            'nombre' => $tallaPrenda,
                            'segmento' => $segmento->name,
                            'genero' => 'Mujer',
                            'categoria' => 'Prendas Adulto'
                        ]);

                        Talla::create([
                            'nombre' => $tallaPrenda,
                            'segmento' => $segmento->name,
                            'genero' => 'Hombre',
                            'categoria' => 'Prendas Infantil'
                        ]);
                        
                    }
                    
                    break;
                
                default:
                    # code...
                    break;
            }

        }

    

        

        


        

       
    }
}
