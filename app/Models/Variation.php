<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;

    protected $table = 'product_variations';

    protected $fillable = ['sku', 'product_id', 'color_id', 'size_id', 'stock', 'precio_especifico', 'oferta', 'precio_oferta', 'img'];

    public function color() {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function size() {
        return $this->belongsTo(Talla::class, 'size_id');
    }

    public function imgGallery() {
        return $this->hasMany(ImageGalleryVariation::class);
    }

}
