<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    // Mostrar el carrito
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    // Añadir producto al carrito
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = Session::get('cart', []);
        
        // Obtener talla del request o usar la del producto por defecto si no se especifica
        // Si viene del detalle y es obligatorio, debería validarse.
        // Para compatibilidad con botones directos (home), si no hay talla, usamos la de la BD o 'Única'
        $talla = $request->input('talla', $product->talla ?? 'Única');

        // Generar ID único para el item en el carrito (ID_Producto + Talla)
        $cartKey = $id . '_' . $talla;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id, // Guardamos referencia al ID real
                'name' => $product->nombre,
                'quantity' => 1,
                'price' => $product->precio,
                'image' => $product->img,
                'size' => $talla
            ];
        }

        Session::put('cart', $cart);
        
        return redirect()->back()->with('success', 'Producto añadido al carrito correctamente!');
    }

    // Actualizar cantidad (opcional, pero buena práctica)
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|numeric|min:1']);
        $cart = Session::get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            Session::put('cart', $cart);
            session()->flash('success', 'Carrito actualizado');
        }
        return redirect()->back();
    }

    // Eliminar producto
    public function remove($id)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Producto eliminado del carrito');
    }
    
    // Vaciar carrito
    public function clear()
    {
        Session::forget('cart');
        return redirect()->back()->with('success', 'Carrito vaciado');
    }
}
