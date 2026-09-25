<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $rawImage = $this->image_url ?: $this->image_path;
        $resolvedImage = null;

        if ($rawImage) {
            if (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://')) {
                $resolvedImage = $rawImage;
            } elseif (str_starts_with($rawImage, '/') || str_starts_with($rawImage, 'tv4/')) {
                $resolvedImage = asset(ltrim($rawImage, '/'));
            } else {
                $resolvedImage = asset('storage/' . ltrim($rawImage, '/'));
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_path' => $resolvedImage,
            'image_url' => $resolvedImage,
            'status' => $this->status,
        ];
    }
}
