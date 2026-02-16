<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderConfirmed;
use Exception;

class CartController extends Controller
{
    // Mostrar el carrito
    public function index()
    {
        $cart = Session::get('cart', []);
        $wishlist = Session::get('wishlist', []);
        $total = 0;
        
        $productIds = array_column($cart, 'product_id');
        $productsModels = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total', 'productsModels', 'wishlist'));
    }

    // Añadir producto al carrito
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = Session::get('cart', []);
        
        // Obtener talla del request o usar una por defecto inteligente
        $talla = $request->input('talla');
        
        if (!$talla) {
            // Intentar usar la de la BD si existe
            if (!empty($product->talla)) {
                $talla = $product->talla;
            } else {
                // Lógica para asignar talla por defecto si se añade desde el listado/home
                $catLower = strtolower($product->categoria);
                if (str_contains($catLower, 'zapatillas') || str_contains($catLower, 'calzado') || str_contains($catLower, 'botas')) {
                    $talla = '42'; // Talla común
                } elseif (str_contains($catLower, 'calcetines')) {
                    $talla = 'M (38-42)';
                } else {
                    $talla = 'M'; // Ropa general
                }
            }
        }

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
                'image' => $product->image_url, // Usamos el accessor dinámico
                'size' => $talla
            ];
        }

        Session::put('cart', $cart);
        
        return redirect()->back()->with('success', 'Producto añadido al carrito correctamente!');
    }

    // Actualizar cantidad y/o talla
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'nullable|numeric|min:1',
            'size' => 'nullable|string'
        ]);

        $cart = Session::get('cart', []);

        if(isset($cart[$id])) {
            $currentQuantity = $request->quantity ?? $cart[$id]['quantity'];
            $currentSize = $request->size ?? $cart[$id]['size'];
            $productId = $cart[$id]['product_id'];

            // Si la talla ha cambiado, el key del carrito también cambia
            $newKey = $productId . '_' . $currentSize;

            if ($newKey !== $id) {
                // Si movemos a una talla que ya existe, sumamos cantidades
                if (isset($cart[$newKey])) {
                    $cart[$newKey]['quantity'] += $currentQuantity;
                } else {
                    // Si no existe, creamos el nuevo key con los datos actualizados
                    $cart[$newKey] = $cart[$id];
                    $cart[$newKey]['size'] = $currentSize;
                    $cart[$newKey]['quantity'] = $currentQuantity;
                }
                // Eliminamos el key antiguo
                unset($cart[$id]);
            } else {
                // Si la talla es la misma, solo actualizamos cantidad
                $cart[$id]['quantity'] = $currentQuantity;
            }

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

    // Mostrar formulario de checkout
    public function checkout()
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.checkout', compact('cart', 'total'));
    }

    // Procesar el pedido
    public function processCheckout(Request $request)
    {
        $request->validate([
            'email' => auth()->check() ? 'nullable|email' : 'required|email|max:255',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'zip' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'card_name' => 'required|string|max:255',
            'card_number' => 'required|string|max:19',
            'card_expiry' => 'required|string|max:5',
            'card_cvv' => 'required|string|max:4',
        ]);

        $cart = Session::get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $shipping = $request->only(['name', 'address', 'city', 'zip', 'phone']);
        $payment = [
            'card_number' => '**** **** **** ' . substr($request->card_number, -4),
            'card_name' => $request->card_name
        ];

        // Determinar el email de destino
        $recipientEmail = auth()->check() ? auth()->user()->email : $request->email;

        // Enviar correo
        try {
            Mail::to($recipientEmail)
                ->send(new OrderConfirmed($cart, $total, $shipping, $payment));
        } catch (Exception $e) {
            Log::error('Error enviando correo de pedido: ' . $e->getMessage());
        }

        // Vaciar carrito
        Session::forget('cart');

        return redirect()->route('home')->with('success', '¡Pedido realizado con éxito! Te hemos enviado un correo de confirmación.');
    }
}
