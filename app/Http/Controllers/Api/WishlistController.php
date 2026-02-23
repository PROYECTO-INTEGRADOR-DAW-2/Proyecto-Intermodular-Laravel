<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $products = $request->user()->wishlist()->get();
        return ProductResource::collection($products);
    }

    public function toggle(Request $request, $id)
    {
        $user = $request->user();
        $product = Product::findOrFail($id);
        
        $attached = $user->wishlist()->toggle($product->id);

        $status = count($attached['attached']) > 0 ? 'added' : 'removed';
        
        return response()->json([
            'status' => $status,
            'message' => $status === 'added' ? 'Producto añadido a la lista de deseos' : 'Producto eliminado de la lista de deseos'
        ]);
    }
}
