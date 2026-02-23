<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: "/api/orders",
        summary: "Llistar comandes de l'usuari autenticat",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Llista de comandes"),
            new OA\Response(response: 401, description: "No autenticat")
        ],
        tags: ["Orders"]
    )]
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product')->latest()->get();
        return response()->json($orders);
    }

    #[OA\Post(
        path: "/api/orders",
        summary: "Crear nova comanda",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["items", "total", "address", "city", "postal_code", "phone"],
                properties: [
                    new OA\Property(property: "items", type: "array", items: new OA\Items(
                        properties: [
                            new OA\Property(property: "product_id", type: "integer"),
                            new OA\Property(property: "quantity", type: "integer"),
                            new OA\Property(property: "size", type: "string")
                        ]
                    )),
                    new OA\Property(property: "total", type: "number", example: 149.99),
                    new OA\Property(property: "address", type: "string", example: "Carrer Major 1"),
                    new OA\Property(property: "city", type: "string", example: "Alcoi"),
                    new OA\Property(property: "postal_code", type: "string", example: "03801"),
                    new OA\Property(property: "phone", type: "string", example: "666123456")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Comanda creada"),
            new OA\Response(response: 422, description: "Validació fallida"),
            new OA\Response(response: 500, description: "Error intern")
        ],
        tags: ["Orders"]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'nullable|string',
            'total' => 'required|numeric',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'phone' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total' => $validated['total'],
                'status' => 'pending',
                'address' => $validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'phone' => $validated['phone'],
            ]);

            foreach ($validated['items'] as $item) {
                // Fetch product to get current price (security)
                $product = Product::find($item['product_id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'size' => $item['size'],
                    'price' => $product->precio,
                    'subtotal' => $product->precio * $item['quantity']
                ]);
            }

            DB::commit();

            return response()->json($order->load('items'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create order', 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Admin: list all orders from all users.
     */
    public function adminIndex()
    {
        $orders = Order::with('items.product', 'user')
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id'          => $order->id,
                    'user'        => $order->user ? $order->user->nombre . ' ' . $order->user->apellidos : 'N/A',
                    'email'       => $order->user?->email,
                    'total'       => $order->total,
                    'status'      => $order->status,
                    'address'     => $order->address,
                    'city'        => $order->city,
                    'postal_code' => $order->postal_code,
                    'items_count' => $order->items->count(),
                    'created_at'  => $order->created_at,
                ];
            });

        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Admin: update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['success' => true, 'data' => $order, 'message' => 'Estado actualizado']);
    }
}
