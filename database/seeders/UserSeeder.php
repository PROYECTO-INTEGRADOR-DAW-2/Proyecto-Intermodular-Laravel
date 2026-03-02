<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserSeeder extends Seeder 
{

    public function run() {
        User::updateOrCreate([
            'nombre' => 'Albert',
            'apellidos' => 'Danga Vicol',
            'email' => 'albertdangavicol@gmail.com',
            'nombre_usuario' => "alvertt",
            'contraseña' => Hash::make("password")
        ]);

        User::factory()->count(10)->create()
    }

}