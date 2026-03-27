<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ReviewFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'user_id', 'valoracion', 'comentario'];


}
