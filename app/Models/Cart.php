<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $casts = [
        'user_id' => 'integer',
        'module_id' => 'integer',
        'item_id' => 'integer',
        'is_guest' => 'boolean',
        'price' => 'float',
        'quantity' => 'integer',
        'add_on_ids' => 'array',
        'add_on_qtys' => 'array',
        'variation' => 'array',
    ];

    protected $fillable = [
        'cart_group', // ✅ MISSING
        'user_id',
        'module_id',
        'item_id',
        'is_guest',
        'add_on_ids',
        'add_on_qtys',
        'item_type',
        'price',
        'quantity',
        'variation',
        'status',
        'type',
        'gift_details',

        // New Fields
        'total_price',
        'offer_type',
        'discount_amount'
    ];

    public function item()
    {
        return $this->morphTo();
    }
    /**
     * Relationship to the User (customer)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
