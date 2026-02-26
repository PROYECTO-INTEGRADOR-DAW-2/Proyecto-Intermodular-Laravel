<?php

namespace App\Repositories;

use App\Models\Product;

class ProductsRepository implements BaseRepository {
    public function getAll() {
        return Product::all();
    }

    public function find($id) {
        return Product::findOrFail($id);
    }

    public function create(array $data) {
        $sku    = $data['sku']    ?? null;
        $nombre = $data['nombre'] ?? null;

        \Illuminate\Support\Facades\Log::info("INTENTANDO CREAR: SKU=" . $sku . " Nombre=" . $nombre);

        // Si ya existe por SKU o por nombre, no hacer nada (no duplicar)
        $exists = Product::where(function($q) use ($sku, $nombre) {
            if ($sku)    $q->orWhere('sku',    $sku);
            if ($nombre) $q->orWhere('nombre', $nombre);
        })->exists();

        if ($exists) {
            \Illuminate\Support\Facades\Log::warning("EL PRODUCTO YA EXISTE (Omitiendo): SKU=" . $sku . " Nombre=" . $nombre);
            return null; // Producto ya existe, se omite
        }

        \Illuminate\Support\Facades\Log::info("INSERTANDO NUEVO PRODUCTO EN BD");
        return Product::create($data);
    }

    public function update($id, array $data) {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id) {
        return Product::destroy($id);
    }
}