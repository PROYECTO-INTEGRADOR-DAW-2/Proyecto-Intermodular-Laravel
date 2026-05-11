<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;



class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $initialRoles = [
            ['rol' => 'client', 'descripcion' => 'Rol predeterminado de cliente'], 
            ['rol' => 'admin', 'descripcion' => 'Rol predeterminado de admin']
        ];

        foreach($initialRoles as $role) {
            Role::create($role);
        }
        











    }
}
