<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Product extends Model
{
    protected $guarded = [];

    public function category (): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class, 'product_id', 'id');
    }

    public function primaryImage (): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id')->where('is_primary', 1);
    }
}
