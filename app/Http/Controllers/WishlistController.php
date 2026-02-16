<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WishlistController extends Controller
{
    // Añadir producto a la lista de deseos
    public function add($id)
    {
        $product = Product::findOrFail($id);
        $wishlist = Session::get('wishlist', []);

        if (!isset($wishlist[$id])) {
            $wishlist[$id] = [
                "name" => $product->nombre,
                "price" => $product->precio,
                "image" => $product->img,
                "id" => $product->id
            ];
            Session::put('wishlist', $wishlist);
            return redirect()->back()->with('success', 'Producto añadido a la lista de deseos');
        }

        return redirect()->back()->with('info', 'El producto ya está en tu lista de deseos');
    }

    // Quitar producto de la lista de deseos
    public function remove($id)
    {
        $wishlist = Session::get('wishlist', []);

        if (isset($wishlist[$id])) {
            unset($wishlist[$id]);
            Session::put('wishlist', $wishlist);
        }

        return redirect()->back()->with('success', 'Producto eliminado de la lista de deseos');
    }

    // Mover de lista de deseos al carrito
    public function moveToCart($id)
    {
        $wishlist = Session::get('wishlist', []);
        
        if (isset($wishlist[$id])) {
            // Añadir al carrito (usando la lógica de CartController o redirigiendo)
            // Por simplicidad redirigimos a la ruta de añadir al carrito
            $this->remove($id);
            return redirect()->route('cart.add', $id)->with('success', 'Producto movido al carrito');
        }

        return redirect()->back()->with('error', 'Producto no encontrado en la lista de deseos');
    }
}
