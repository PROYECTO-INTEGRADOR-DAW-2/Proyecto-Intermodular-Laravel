<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(
            Product::query()->paginate(10)
        );
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validate();

        $product = \App\Models\Product::create($data);

        return response()->json($product->load('product'), 201);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return new ProductRequest($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent(); // 204
    }
}