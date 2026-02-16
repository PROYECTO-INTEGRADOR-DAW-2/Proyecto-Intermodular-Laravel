<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Pedido</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { width: 90%; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { text-align: center; border-bottom: 2px solid #E53935; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { color: #E53935; font-size: 24px; font-weight: bold; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f9f9f9; }
        .total { font-weight: bold; font-size: 18px; text-align: right; margin-top: 20px; }
        .shipping-info { background-color: #f3f4f6; padding: 15px; border-radius: 4px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">J&A Sports</div>
            <h2>¡Gracias por tu pedido!</h2>
        </div>

        <p>Hola <strong>{{ $shipping['name'] }}</strong>,</p>
        <p>Hemos recibido tu pedido correctamente. Aquí tienes los detalles:</p>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $item)
                <tr>
                    <td>{{ $item['name'] }} ({{ $item['size'] }})</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['price'], 2) }} €</td>
                    <td>{{ number_format($item['price'] * $item['quantity'], 2) }} €</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Total: {{ number_format($total, 2) }} €
        </div>

        <div class="shipping-info">
            <h3>Datos de Envío</h3>
            <p><strong>Dirección:</strong> {{ $shipping['address'] }}</p>
            <p><strong>Ciudad:</strong> {{ $shipping['city'] }} ({{ $shipping['zip'] }})</p>
            <p><strong>Teléfono:</strong> {{ $shipping['phone'] }}</p>
        </div>

        <div class="shipping-info" style="margin-top: 10px;">
            <h3>Información de Pago</h3>
            <p><strong>Titular:</strong> {{ $payment['card_name'] }}</p>
            <p><strong>Tarjeta:</strong> {{ $payment['card_number'] }}</p>
        </div>

        <p>Te avisaremos cuando tu pedido esté en camino.</p>

        <div class="footer">
            &copy; {{ date('Y') }} J&A Sports. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
