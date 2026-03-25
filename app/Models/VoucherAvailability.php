<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'voucher_id',
        'status',
        'active_at',
    ];

    // Optional: relationships
    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function voucher() {
        return $this->belongsTo(Item::class);
    }

      public static function getForVoucherBranches(int $voucherId, array $branchIds)
    {
        return self::where('voucher_id', $voucherId)
            ->whereIn('store_id', $branchIds)
            ->get();
    }
}