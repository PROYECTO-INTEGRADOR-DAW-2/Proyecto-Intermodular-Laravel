<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Database\Factories\ReviewFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'user_id', 'valoracion', 'comentario'];


    public function user() {
        return $this->belongsTo(User::class);
    }

}
