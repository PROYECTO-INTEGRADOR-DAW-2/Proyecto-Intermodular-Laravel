<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Product",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "sku", type: "string", example: "SKU001"),
        new OA\Property(property: "marca", type: "string", example: "Nike"),
        new OA\Property(property: "categoria", type: "string", example: "Zapatillas"),
        new OA\Property(property: "nombre", type: "string", example: "Nike Air Zoom"),
        new OA\Property(property: "precio", type: "number", format: "float", example: 99.99),
        new OA\Property(property: "talla", type: "string", example: "42"),
        new OA\Property(property: "color", type: "string", example: "Rojo"),
        new OA\Property(property: "stock", type: "integer", example: 50),
        new OA\Property(property: "sexo", type: "string", enum: ["Hombre", "Mujer", "Niños", "Unisex"]),
        new OA\Property(property: "oferta", type: "boolean", example: false),
        new OA\Property(property: "image_url", type: "string", example: "http://localhost:8000/imgNike/foto.png")
    ]
)]
class ProductController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "Llistar productes (amb filtres i paginació)",
        parameters: [
            new OA\Parameter(name: "search", in: "query", description: "Cerca per nom o SKU", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "sexo", in: "query", description: "Filtrar per gènere", schema: new OA\Schema(type: "string", enum: ["Hombre", "Mujer", "Niños", "Unisex"])),
            new OA\Parameter(name: "marca", in: "query", description: "Filtrar per marca", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "categoria", in: "query", description: "Filtrar per categoria", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "min_price", in: "query", description: "Preu mínim", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "max_price", in: "query", description: "Preu màxim", schema: new OA\Schema(type: "number")),
            new OA\Parameter(name: "oferta", in: "query", description: "Només ofertes", schema: new OA\Schema(type: "boolean")),
            new OA\Parameter(name: "page", in: "query", description: "Pàgina", schema: new OA\Schema(type: "integer", default: 1))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Llista paginada de productes",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Product")),
                        new OA\Property(property: "meta", type: "object")
                    ]
                )
            )
        ],
        tags: ["Products"]
    )]
    public function index(Request $request)
    {
        $query = Product::query();

        // Aplicar filtros
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('sexo')) {
            $sexos = is_array($request->sexo) ? $request->sexo : explode(',', $request->sexo);
            // Logic for 'Hombre'/'Mujer' including 'Unisex'
            if (in_array('Hombre', $sexos) || in_array('Mujer', $sexos)) {
                if (!in_array('Unisex', $sexos)) {
                    $sexos[] = 'Unisex';
                }
            }
            $query->whereIn('sexo', $sexos);
        }

        if ($request->filled('marca')) {
            $marcas = is_array($request->marca) ? $request->marca : explode(',', $request->marca);
            $query->whereIn('marca', $marcas);
        }

        if ($request->filled('categoria')) {
            $cats = is_array($request->categoria) ? $request->categoria : explode(',', $request->categoria);
            $query->whereIn('categoria', $cats);
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

        if ($request->filled('talla')) {
            $tallas = is_array($request->talla) ? $request->talla : explode(',', $request->talla);
            $query->whereIn('talla', $tallas);
        }

        $products = $query->paginate(12);

        // Metadata for filters
        $metadata = [
            'marcas' => Product::select('marca')->distinct()->pluck('marca')->filter(),
            'categorias' => Product::select('categoria')->distinct()->pluck('categoria')->filter(),
            'sexos' => Product::select('sexo')->distinct()->pluck('sexo')->filter(),
            'tallas' => Product::select('talla')->distinct()->pluck('talla')->filter()->sort()->values(),
            'maxPrice' => Product::max('precio') ?? 1000
        ];

        return ProductResource::collection($products)->additional(['meta' => $metadata]);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Detall d'un producte",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Producte trobat", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=404, description="No trobat")
     * )
     */
    public function show(Product $product)
    {
        $product->load('reviews.user');
        
        $relatedProducts = Product::where('categoria', $product->categoria)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return new ProductResource($product);
    }

    public function home()
    {
        $masComprados = Product::inRandomOrder()->take(4)->get();
        // Promoted product (Nike Gato)
        $promotedProduct = Product::where('nombre', 'like', '%nike gato%')->first();

        return response()->json([
            'masComprados' => ProductResource::collection($masComprados),
            'promotedProduct' => $promotedProduct ? new ProductResource($promotedProduct) : null
        ]);
    }

    #[OA\Post(
        path: "/api/admin/products",
        summary: "Crear producte (admin)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: "#/components/schemas/Product")),
        responses: [
            new OA\Response(response: 201, description: "Producte creat"),
            new OA\Response(response: 401, description: "No autenticat"),
            new OA\Response(response: 403, description: "Sense permisos")
        ],
        tags: ["Products"]
    )]
    public function store(ProductRequest $request)
    {
        $data = $request->validate(); // ProductRequest handles validation
        $product = Product::create($data);
        return response()->json($product, 201);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}