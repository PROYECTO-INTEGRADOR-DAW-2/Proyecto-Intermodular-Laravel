<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Color;


class ColorSeeder extends Seeder
{

    protected $coloresDisponibles = [
        ["Nombre" => 'Rojo', "Hex" => "#C53030", "Prefijo" => "ROJ"],
        ["Nombre" => 'Azul', "Hex" => "#2B6CB0", "Prefijo" => "AZU"],
        ["Nombre" => 'Amarillo', "Hex" => "#D69E2E", "Prefijo" => "AMA"],
        ["Nombre" => 'Beige', "Hex" => "#E2E8F0", "Prefijo" => "BEI"],
        ["Nombre" => 'Verde', "Hex" => "#2F855A", "Prefijo" => "VER"],
        ["Nombre" => 'Marron', "Hex" => "#744210", "Prefijo" => "MAR"],
        ["Nombre" => 'Negro', "Hex" => "#1A202C", "Prefijo" => "NEG"],
        ["Nombre" => 'Blanco', "Hex" => "#F7FAFC", "Prefijo" => "BLA"],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect($this->coloresDisponibles)->each(fn($color) => Color::create([
            'nombre' => $color['Nombre'],
            'código_hex' => $color['Hex'],
            'prefijo' => $color['Prefijo']
        ]));
    }
}
