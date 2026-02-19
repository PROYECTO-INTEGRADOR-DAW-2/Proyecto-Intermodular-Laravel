<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // ... (existing index logic) ...
        // Iniciar la consulta
        $query = Product::query();

        // Aplicar filtros si existen en la request
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('sexo')) {
            $sexosFiltrados = $request->sexo;
            
            if (in_array('Hombre', $sexosFiltrados) || in_array('Mujer', $sexosFiltrados)) {
                if (!in_array('Unisex', $sexosFiltrados)) {
                    $sexosFiltrados[] = 'Unisex';
                }
            }

            $query->whereIn('sexo', $sexosFiltrados);
        }

        if ($request->filled('marca')) {
            $query->whereIn('marca', $request->marca);
        }

        if ($request->filled('categoria')) {
            $query->whereIn('categoria', $request->categoria);
        }

        if ($request->filled('min_price')) {
            $query->where('precio', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('precio', '<=', $request->max_price);
        }

        if ($request->filled('oferta')) {
            $query->where('oferta', true);
        }

        // Obtener productos filtrados
        $products = $query->get();

        // Obtener opciones para los filtros
        $marcas = Product::select('marca')->distinct()->pluck('marca')->filter();
        $categorias = Product::select('categoria')->distinct()->pluck('categoria')->filter();
        $sexos = Product::select('sexo')->distinct()->pluck('sexo')->filter();

        // Max price for slider
        $maxPrice = Product::max('precio') ?? 1000;

        return view('products.index', compact('products', 'marcas', 'categorias', 'sexos', 'maxPrice'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        // Productos relacionados (misma categoría, excluyendo el actual)
        $relatedProducts = Product::where('categoria', $product->categoria)
            ->where('id', '!=', $id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function home()
    {
        // Obtener 4 productos aleatorios para la sección "Más comprados"
        $masComprados = Product::inRandomOrder()->take(4)->get();

        // Obtener el producto promocionado para el banner (Nike Gato)
        $promotedProduct = Product::where('nombre', 'like', '%nike gato%')->first();
        
        return view('welcome', compact('masComprados', 'promotedProduct'));
    }
}
