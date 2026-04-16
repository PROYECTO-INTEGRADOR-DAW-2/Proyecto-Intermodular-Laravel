<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseController extends Controller
{
    protected function sendResponse(mixed $result, $message, $code = 200)
    {
        if ($result instanceof JsonResource) {
            return response()->json([
                "success" => true,
                "data" => $result->response()->getData(true)['data'],
                "meta" => $result->response()->getData(true)['meta'] ?? [],
                "message" => $message,
            ], $code);
        } else {
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => $message,
            ], $code);
        }
    }


    protected function sendError($error, $info = [], $code = 400)
    {
        $payload = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($info)) {
            $payload['info'] = $info;
        }

        return response()->json($payload, $code);
    }

    protected function sendMessage($message, $code)
    {

        $payload = [
            'success' => true,
            'message' => $message,
        ];

        return response()->json($payload, $code);
    }

}