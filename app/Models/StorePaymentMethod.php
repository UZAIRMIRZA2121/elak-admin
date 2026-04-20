<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorePaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'store_payment_methods';

    protected $fillable = [
        'store_id',
        'method_id',
        'value',
    ];

    protected $casts = [
        'value' => 'array', // auto convert JSON ↔ array
    ];

    // Relations
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }
}