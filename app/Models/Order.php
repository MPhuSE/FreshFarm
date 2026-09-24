<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['order_code', 'user_id', 'coupon_id', 'subtotal', 'discount_amount', 'shipping_fee', 'grand_total', 'status', 'payment_method', 'payment_status', 'transaction_ref', 'shipping_address', 'recipient_name', 'phone', 'carrier', 'tracking_code', 'note'])]
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'coupon_id',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'grand_total',
        'status',
        'payment_method',
        'payment_status',
        'transaction_ref',
        'shipping_address',
        'recipient_name',
        'phone',
        'carrier',
        'tracking_code',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
