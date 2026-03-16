<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = ContactMessage::create($validated);

        try {
            \Illuminate\Support\Facades\Http::post('http://172.16.211.88:5678/webhook-test/a83c266c-b550-44c8-a85d-6faed7668184', $validated);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending webhook to n8n: ' . $e->getMessage());
        }

        return response()->json([
            'message' => '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.',
            'data'    => $contact
        ], 201);
    }

    public function index()
    {
        $messages = ContactMessage::latest()->get();
        return response()->json($messages);
    }
}
