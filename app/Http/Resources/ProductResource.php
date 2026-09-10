<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'unit' => $this->unit,
            'price' => (int) $this->price,
            'origin' => $this->origin,
            'available_quantity' => $this->relationLoaded('inventory')
                ? (int) $this->available_quantity
                : 0,
            'status' => $this->status,
            'primary_image_url' => $this->resolvePrimaryImageUrl(),
        ];
    }

    protected function resolvePrimaryImageUrl(): ?string
    {
        $image = null;

        if ($this->relationLoaded('primaryImage')) {
            $image = $this->primaryImage;
        } elseif ($this->relationLoaded('images')) {
            $image = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        }

        return $image ? asset('storage/'.$image->file_path) : null;
    }
}