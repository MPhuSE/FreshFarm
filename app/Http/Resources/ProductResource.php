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
            'compare_at_price' => (int) $this->compare_at_price,
            'origin' => $this->origin,
            'featured' => (bool) $this->featured,
            'available_quantity' => $this->relationLoaded('inventory')
                ? (int) $this->available_quantity
                : 0,
            'status' => $this->status,
            'primary_image_url' => $this->resolvePrimaryImageUrl(),
            'rating_avg' => $this->reviews_avg_rating ? (float) $this->reviews_avg_rating : 0,
            'reviews_count' => $this->reviews_count ?? 0,
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

        if ($image) {
            if (str_starts_with($image->file_path, 'http')) {
                return $image->file_path;
            }
            if (str_starts_with($image->file_path, '/') || str_starts_with($image->file_path, 'tv4/')) {
                return asset(ltrim($image->file_path, '/'));
            }
            return asset('storage/'.$image->file_path);
        }

        return null;
    }
}
