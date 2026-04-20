<?php

use App\Http\Controllers\Api\BaseController;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class PedidoController extends BaseController {

    public function checkout(Request $request, PayPalService $payPalService)
    {
        // 1. Validar carrito y calcular total
        $total = 100.50; 

        // 2. Si el método es PayPal, llamar al servicio
        if ($request->metodo_pago === 'paypal') {
            $orden = $payPalService->crearOrden($total);
            
            // Devolvemos el ID de PayPal al frontend
            return response()->json([
                'id' => $orden['id'] 
            ]);
        }
        
    }

    public function confirmarPago(Request $request, PayPalService $payPalService)
    {
        $orderId = $request->paypal_order_id;

        // 1. Llamamos a PayPal para cobrar de verdad
        $resultado = $payPalService->capturarOrden($orderId);

        // 2. Verificamos que PayPal diga que todo está OK
        if (isset($resultado['status']) && $resultado['status'] === 'COMPLETED') {
            
            // 3. AQUÍ es donde creas el pedido en tu DB
            $pedido = Pedido::create([
                'user_id' => auth()->id(),
                'total' => $resultado['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
                'estado' => 'pagado',
                'transaction_id' => $resultado['id'] // ID de transacción de PayPal
            ]);

            return response()->json(['message' => 'Pedido creado con éxito', 'pedido' => $pedido]);
        }

        return response()->json(['error' => 'El pago no se pudo completar'], 400);
    }
}