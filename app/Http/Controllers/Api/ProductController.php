<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;



class ProductController extends Controller
{
    public function index(Request $request)
    {
        
        $query = Product::query();

        // 1. Para el nombre (string)
        $query->when($request->input('nombre'), function ($q, $input) {
            $q->where('nombre', 'like', '%' . $input . '%');
        });

        // 2. Para la categoría (array)
        $query->when($request->input('categoria'), function ($q, $input) {
            // Importante: $input aquí ya es el array que viene del request
            $q->whereIn('categoria', (array) $input); 
        });

        $query->when($request->input('marca'), function ($q, $input) {
            // Importante: $input aquí ya es el array que viene del request
            $q->whereIn('marca', (array) $input); 
        });

        $query->when($request->input('deporte'), function ($q, $input) {
            // Importante: $input aquí ya es el array que viene del request
            $q->whereIn('deporte', (array) $input); 
        });

        $query->when($request->input('altura'), function ($q, $input) {
            // Importante: $input aquí ya es el array que viene del request
            $q->whereIn('altura', (array) $input); 
        });

        $query->when($request->input('sexo'), function ($q, $input) {
            // Importante: $input aquí ya es el array que viene del request
            $q->whereIn('sexo', (array) $input); 
        });

        $query->when($request->input('precio_max'), function ($q, $input) {
            // Importante: $input aquí ya es el array que viene del request
            $q->where('precio', '<=', (integer) $input);
        });

        $maxPrice = $query->max('precio');


        $productos = $query->paginate(10);

        return ProductResource::collection($productos)->additional(['meta' => [
            ['max_price' => $maxPrice]
        ]]);

    }


    // FUNCION POR TERMINAR
    public function getMostPurchasedProducts() {

        


    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        $product = Product::create($data);

        return response()->json($product->load('product'), 201);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        //return new ProductRequest($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent(); // 204
    }
}