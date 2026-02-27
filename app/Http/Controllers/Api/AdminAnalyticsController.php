<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\Product;

class AdminAnalyticsController extends Controller
{
    /**
     * Return a summary of analytics to be displayed on the Admin Panel.
     * Specifically, returns the Top 5 best selling products.
     */
    public function summary()
    {
        // Get the top 5 products by total quantity sold
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('product') // Load the product details
            ->get()
            ->map(function ($item) {
                // Ensure the product exists before accessing its properties
                if ($item->product) {
                    return [
                        'id' => $item->product->id,
                        'name' => $item->product->nombre,
                        'brand' => $item->product->marca,
                        'image_url' => $item->product->image_url,
                        'total_sold' => (int)$item->total_sold
                    ];
                }
                return null;
            })
            ->filter() // Remove nulls in case a product was hard-deleted
            ->values();

        return response()->json([
            'success' => true,
            'top_products' => $topProducts
        ]);
    }
}
