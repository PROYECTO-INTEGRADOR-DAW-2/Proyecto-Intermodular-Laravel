<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ProductFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'marca', 'categoria', 'nombre', 'precio', 'talla', 'color', 'stock', 'ajuste', 'sexo', 'descripcion', 'altura', 'deporte', 'oferta', 'img', 'is_eco'];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function getImageUrlAttribute()
    {
        if (!$this->img) return null;
        if (\Illuminate\Support\Str::startsWith($this->img, ['http://', 'https://'])) return $this->img;

        // Si el nombre de la imagen ya incluye la ruta (ej: 'imgNike/foto.png' o 'imgAdidas/zapatillas/foto.png'), la respetamos
        if (\Illuminate\Support\Str::startsWith($this->img, ['img', 'imgNike', 'imgAdidas', 'imgPuma', 'imgAsics'])) {
            return asset($this->img);
        }

        $marca = strtolower($this->marca);
        $folder = 'img'; // Default folder

        if (str_contains($marca, 'nike')) {
            $folder = 'imgNike';
        } elseif (str_contains($marca, 'adidas')) {
            $folder = 'imgAdidas';
        } elseif (str_contains($marca, 'puma')) {
            $folder = 'imgPuma';
        } elseif (str_contains($marca, 'asics')) {
            $folder = 'imgAsics';
        }

        // Check if image exists in category subfolder
        if ($this->categoria) {
            $categoryFolder = strtolower($this->categoria);
            $pathWithCategory = $folder . '/' . $categoryFolder . '/' . $this->img;
            
            // Check existence in public folder
            if (file_exists(public_path($pathWithCategory))) {
                return asset($pathWithCategory);
            }
        }

        // Fallback to brand folder
        return asset($folder . '/' . $this->img);
    }
}
