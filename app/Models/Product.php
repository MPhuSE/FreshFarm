<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
use SoftDeletes;

    protected $fillable = [
        'category_id', 'sku', 'name', 'slug', 'unit', 'origin',
        'price', 'compare_at_price', 'short_description',
        'description_html', 'status', 'featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'featured' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * available_quantity = quantity_on_hand - quantity_reserved
     * Yêu cầu đã eager-load quan hệ inventory, nếu chưa load trả về 0
     * để tránh N+1 query ngầm khi render danh sách.
     */
    public function getAvailableQuantityAttribute(): int
    {
        if (! $this->relationLoaded('inventory') || ! $this->inventory) {
            return 0;
        }

        return (int) ($this->inventory->quantity_on_hand - $this->inventory->quantity_reserved);
    }
}
