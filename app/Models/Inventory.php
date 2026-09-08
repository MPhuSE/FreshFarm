<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'product_id';
    public $incrementing = false;
    const CREATED_AT = null;

    protected $fillable = [
        'product_id',
        'quantity_on_hand',
        'quantity_reserved',
        'reorder_level',
    ];
}
