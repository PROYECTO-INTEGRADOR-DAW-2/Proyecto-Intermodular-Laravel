<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageGalleryVariation extends Model
{

    protected $table = 'image_gallery_variations';

    protected $fillable = ['variation_id', 'nombre', 'orden'];

    
}
