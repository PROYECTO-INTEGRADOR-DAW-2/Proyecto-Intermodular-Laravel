<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin',  'description' => 'Administrator with full access'],
            ['name' => 'vendor', 'description' => 'Vendor who can manage their own products'],
            ['name' => 'editor', 'description' => 'Editor who can moderate content'],
            ['name' => 'user',   'description' => 'Regular user with basic access'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate(['name' => $role['name']], $role);
        }

        // Assign 'admin' role to the first user
        $adminRole = Role::where('name', 'admin')->first();
        $firstUser = User::first();

        if ($firstUser && $adminRole) {
            if (!$firstUser->roles()->where('role_id', $adminRole->id)->exists()) {
                $firstUser->roles()->attach($adminRole->id);
            }
            // Also update the direct role column
            $firstUser->update(['role' => 'admin']);
        }
    }
}
