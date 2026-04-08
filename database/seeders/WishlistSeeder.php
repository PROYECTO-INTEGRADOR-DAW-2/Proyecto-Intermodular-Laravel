<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WishlistItem;

class WishlistSeederSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WishlistItem::factory()
        ->count(30)
        ->create();
    }
}
