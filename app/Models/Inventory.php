<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $primaryKey = 'product_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'product_id', 'quantity_on_hand', 'quantity_reserved', 'reorder_level',
    ];

    protected $casts = [
        'quantity_on_hand' => 'decimal:3',
        'quantity_reserved' => 'decimal:3',
        'reorder_level' => 'decimal:3',
        'updated_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAvailableQuantityAttribute(): float
    {
        return $this->quantity_on_hand - $this->quantity_reserved;
    }
}