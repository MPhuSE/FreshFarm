<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProductDetailResource extends ProductResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'description_html' => $this->description_html,
        ]);
    }
}