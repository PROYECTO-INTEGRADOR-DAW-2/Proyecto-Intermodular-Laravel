<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProfileController extends BaseController{

    public function getWishlistFromUser(Request $request) {

        $user = $request->user();

        if($user) {
            $wishlist = $user->wishlist();

            return $this->sendResponse($wishlist, "La lista de deseos se ha obtenido", 200);
            
        } else {
            return $this->sendError("Usuario no logueado", [], 402);
        }

    }

    public function toggle(Request $request) {
        $user = $request->user();
        $productId = $request->product_id;


        if (!$productId) {
            return $this->sendError("Producto no encontrado", [], 404);
        }

        if (!$user) {
            return $this->sendError("Usuario no logueado", [], 402);
        }

        $exists = $user->wishlist()->where('product_id', $productId)->exists();

        if ($exists) {
            $user->wishlist()->detach($productId);
            return $this->sendMessage("Producto eliminado de la lista de deseos", 200);
        }

        $user->wishlist()->attach($productId);
        return $this->sendMessage("Producto añadido a la lista de deseos", 200);
        


    }
}