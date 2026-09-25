<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $filePath = (string) $this->file_path;
        $url = null;

        if (str_starts_with($filePath, 'http://') || str_starts_with($filePath, 'https://')) {
            $url = $filePath;
        } elseif (str_starts_with($filePath, '/') || str_starts_with($filePath, 'tv4/')) {
            $url = asset(ltrim($filePath, '/'));
        } else {
            $url = asset('storage/' . ltrim($filePath, '/'));
        }

        return [
            'id' => $this->id,
            'url' => $url,
            'alt_text' => $this->alt_text,
            'is_primary' => (bool) $this->is_primary,
            'sort_order' => $this->sort_order,
        ];
    }
}
