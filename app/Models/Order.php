<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\OrderFactory;
use App\Enums\OrderStatusEnum;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id', 
        'status', 
        'subtotal', 
        'shipping_cost', 
        'tax_amount', 
        'total_amount', 
        'shipping_address', 
        'payment_method'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

}
