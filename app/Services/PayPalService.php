<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayPalService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.paypal.base_url');
    }

    // Paso A: Obtener el Access Token (PayPal lo pide para cada operación)
    private function getAccessToken()
    {
        $response = Http::withBasicAuth(
            config('services.paypal.client_id'),
            config('services.paypal.secret')
        )->asForm()->post("{$this->baseUrl}/v1/oauth2/token", [
            'grant_type' => 'client_credentials',
        ]);

        return $response->json()['access_token'];
    }

    // Paso B: Crear la orden en los servidores de PayPal
    public function crearOrden($monto)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)->post("{$this->baseUrl}/v2/checkout/orders", [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "EUR",
                    "value" => number_format($monto, 2, '.', '')
                ]
            ]]
        ]);

        return $response->json(); // Aquí viene el ID de la orden
    }

    public function capturarOrden($orderId)
    {
        $accessToken = $this->getAccessToken();

        // Importante: La URL cambia a /capture
        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        return $response->json();
    }

    
}