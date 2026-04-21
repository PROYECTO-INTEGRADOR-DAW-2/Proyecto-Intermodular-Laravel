<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;

    protected $table = 'product_variations';

    protected $fillable = ['product_id', 'color_id', 'size_id', 'stock', 'precio_especifico'];

    public function color() {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function size() {
        return $this->belongsTo(Talla::class, 'size_id');
    }

}
