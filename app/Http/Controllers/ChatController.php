<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage = $request->input('message');

        try {
            // 1. Obtener productos de la base de datos para el contexto
            // Limitamos los campos para no exceder tokens innecesariamente
            $products = Product::all(['nombre', 'precio', 'marca', 'categoria', 'descripcion', 'stock', 'oferta'])->toJson();

            if (!config('openai.api_key')) {
                return response()->json(['response' => 'System: Falta la API Key de OpenAI. Configure OPENAI_API_KEY en su archivo .env.'], 200);
            }

            // 2. Crear el prompt del sistema con los datos de los productos
            $systemPrompt = "Eres un asistente virtual útil y educado para el sitio web de comercio electrónico 'J&A Sports'. " .
                "Tu ÚNICA función es ayudar a los usuarios con información sobre los productos disponibles en nuestra base de datos. " .
                "A continuación se muestra la lista ACTUAL de productos en formato JSON:\n\n" .
                $products . "\n\n" .
                "INSTRUCCIONES ESTRICTAS:\n" .
                "1. SOLO responde preguntas basadas en la lista de productos anterior.\n" .
                "2. NO inventes productos ni precios.\n" .
                "3. Si un usuario pregunta por un producto que no está en la lista, di cortésmente que no lo tenemos disponible.\n" .
                "4. NO respondas preguntas sobre temas generales (historia, matemáticas, política, etc.). Si te preguntan sobre eso, di que solo puedes responder sobre productos de J&A Sports.\n" .
                "5. Sé breve, conciso y promueve la venta.";

            // 3. Llamar a la API de OpenAI
            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage
                    ],
                ],
                'temperature' => 0.3, // Temperatura baja para respuestas más factuales
            ]);

            return response()->json([
                'response' => $result->choices[0]->message->content
            ]);

        } catch (\Throwable $e) {
            Log::error('OpenAI Error: ' . $e->getMessage());
            return response()->json(['response' => 'Error: No puedo responder en este momento. (' . $e->getMessage() . ')'], 200);
        }
    }
}