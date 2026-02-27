<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'marca' => $this->marca,
            'categoria' => $this->categoria,
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'talla' => $this->talla,
            'color' => $this->color,
            'stock' => $this->stock,
            'ajuste' => $this->ajuste,
            'sexo' => $this->sexo,
            'descripcion' => $this->descripcion,
            'altura' => $this->altura,
            'deporte' => $this->deporte,
            'oferta' => $this->oferta,
            'is_eco' => (bool) $this->is_eco,
            'img' => $this->img,
            'image_url' => $this->image_url, // Also adding image_url accessor
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'reviews' => $this->whenLoaded('reviews'),
        ];
    }
}
