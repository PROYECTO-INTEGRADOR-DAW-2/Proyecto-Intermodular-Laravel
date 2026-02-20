<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute($value)
    {
        // If it's already a full URL, return it
        if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        // If it already has the path logic (e.g. imgNike/...), return asset
        if (\Illuminate\Support\Str::startsWith($value, ['img', 'imgNike', 'imgAdidas', 'imgPuma', 'imgAsics'])) {
            return asset($value);
        }

        // Otherwise, resolve using parent product logic
        if (!$this->product) {
            return asset('img/' . $value); // Fallback if no product loaded
        }

        $product = $this->product;
        $marca = strtolower($product->marca);
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
        if ($product->categoria) {
            $categoryFolder = strtolower($product->categoria);
            $pathWithCategory = $folder . '/' . $categoryFolder . '/' . $value;
            
            // Check existence in public folder
            if (file_exists(public_path($pathWithCategory))) {
                return asset($pathWithCategory);
            }
        }

        // Fallback to brand folder
        return asset($folder . '/' . $value);
    }
}
