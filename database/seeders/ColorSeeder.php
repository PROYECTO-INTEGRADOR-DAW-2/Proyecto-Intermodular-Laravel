<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Color;


class ColorSeeder extends Seeder
{

    protected $coloresDisponibles = [
        ["Nombre" => 'Rojo', "Hex" => "#C53030"],
        ["Nombre" => 'Azul', "Hex" => "#2B6CB0"],
        ["Nombre" => 'Amarillo', "Hex" => "#D69E2E"],
        ["Nombre" => 'Beige', "Hex" => "#E2E8F0"],
        ["Nombre" => 'Verde', "Hex" => "#2F855A"],
        ["Nombre" => 'Marron', "Hex" => "#744210"],
        ["Nombre" => 'Negro', "Hex" => "#1A202C"],
        ["Nombre" => 'Blanco', "Hex" => "#F7FAFC"],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect($this->coloresDisponibles)->each(fn($color) => Color::create([
            'nombre' => $color['Nombre'],
            'código_hex' => $color['Hex']
        ]));
    }
}
