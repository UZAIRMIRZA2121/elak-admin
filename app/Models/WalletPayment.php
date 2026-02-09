<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletPayment extends Model
{
    use HasFactory;

    // 👇 Make sure this matches your actual table name
    protected $table = 'wallet_payments';

    // 👇 Allow mass assignment
    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'payment_status',
        'payment_method',
    ];

    // 👇 If your table does NOT have created_at & updated_at
    // public $timestamps = false;

    // 👇 Optional: cast types correctly
    protected $casts = [
        'amount' => 'float',
        'user_id' => 'integer',
    ];

    // 👇 Optional relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
