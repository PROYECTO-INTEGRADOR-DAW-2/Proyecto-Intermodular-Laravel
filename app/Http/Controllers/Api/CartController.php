<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Simple session/cache based cart for API demo. 
    // Ideally this should use a database table 'carts' and 'cart_items' linked to user or session_id.
    // For now, we will assume the frontend manages the cart state or we use a simple DB approach if tables exist.
    // Given the request to "port" the existing logic which used session(), we can't easily use PHP session APIs for a decoupled Vue app potentially.
    // However, if using Sanctum stateful (SPA mode), sessions *do* work.
    
    // But since we are building a "clean" API, let's make it returning data the frontend can store (LocalStorage) or use a DB cart.
    // Let's implement a DB-backed cart for authenticated users, and assume frontend handles guest cart or we provide endpoints for it.
    
    // Actually, looking at the previous logic, it used `session('cart')`. API is stateless.
    // Recommendation: Frontend manages cart in Pinia/LocalStorage.
    // Backend purely provides "Sync" or "Checkout" endpoints.
    // Or, we create a Cart model.
    
    // Simplest path for "Porting":
    // The previous `CartController` had `add`, `update`, `remove`, `clear`.
    // I'll re-implement these to return the updated cart state JSON, using Cache or DB if possible, or just purely calculation endpoints.
    
    // Let's go with: Frontend sends cart items to "validate" or "fetch details".
    // Endpoint: POST /api/cart/details -> receives [{id, qty, size}] -> returns full product details with prices.
    
    public function getDetails(Request $request)
    {
        $items = $request->input('items', []); // [{id: 1, quantity: 2, size: 42}, ...]
        
        $cartDetails = [];
        $total = 0;

        foreach ($items as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                // Determine image URL
                $imageUrl = $product->image_url; // Accessor likely exists or logic needed
                if (!$imageUrl && $product->img) {
                     // Replicating logic from ProductController/Blade
                     $brandFolder = "img" . ucfirst(mb_strtolower($product->marca ?? 'nike')); // Fallback logic
                     $type = mb_strtolower($product->categoria ?? 'general');
                     $imageUrl = asset("$brandFolder/$type/{$product->img}");
                }

                $subtotal = $product->precio * $item['quantity'];
                $total += $subtotal;

                $cartDetails[] = [
                    'product_id' => $product->id,
                    'name' => $product->nombre,
                    'price' => $product->precio,
                    'image' => $imageUrl,
                    'quantity' => $item['quantity'],
                    'size' => $item['size'] ?? null,
                    'subtotal' => $subtotal,
                    'stock' => $product->stock
                ];
            }
        }

        return response()->json([
            'items' => $cartDetails,
            'total' => $total
        ]);
    }
}
