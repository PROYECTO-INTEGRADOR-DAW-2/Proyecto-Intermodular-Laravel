<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Variation;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'marca', 'categoria', 'nombre', 'precio', 'talla', 'color', 'stock', 'ajuste', 'sexo', 'descripcion', 'altura', 'deporte', 'oferta', 'novedad', 'img'];

    protected $casts = [
        'oferta' => 'boolean',
        'novedad' => 'boolean',
    ];

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function variations() {
        return $this->hasMany(Variation::class)->with(['color', 'size']);
    }

}
