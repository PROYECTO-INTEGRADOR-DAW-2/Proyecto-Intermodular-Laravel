<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ProductFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'marca', 'categoria', 'nombre', 'precio', 'talla', 'color', 'stock', 'ajuste', 'sexo', 'descripcion', 'altura', 'deporte', 'oferta', 'img'];

    protected $casts = [
        'oferta' => 'boolean',
    ];

}
