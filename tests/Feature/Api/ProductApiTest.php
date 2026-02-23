<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listing products.
     */
    public function test_can_list_products(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'sku',
                        'marca',
                        'categoria',
                        'nombre',
                        'precio',
                        'talla',
                        'color',
                        'stock',
                        'sexo',
                        'oferta',
                        'image_url',
                    ]
                ],
                'links',
                'meta' => [
                    'marcas',
                    'categorias',
                    'sexos',
                    'tallas',
                    'maxPrice'
                ]
            ]);
    }

    /**
     * Test filtering products by brand.
     */
    public function test_can_filter_products_by_brand(): void
    {
        Product::factory()->create(['marca' => 'Nike']);
        Product::factory()->create(['marca' => 'Adidas']);

        $response = $this->getJson('/api/products?marca=Nike');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.marca', 'Nike');
    }

    /**
     * Test viewing a single product.
     */
    public function test_can_show_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.nombre', $product->nombre);
    }
}
