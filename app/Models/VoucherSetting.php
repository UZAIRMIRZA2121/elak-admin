<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherSetting extends Model
{
    use HasFactory;

    protected $table = "voucher_settings";
        protected $fillable = [
            'item_id' ,
            'validity_period' ,
            'specific_days_of_week' ,
            'holidays_occasions' ,
            'custom_blackout_dates' ,
            'age_restriction' ,
            'group_size_requirement' ,
            'usage_limit_per_user' ,
            'usage_limit_per_store' ,
            'offer_validity_after_purchase' ,
            'general_restrictions' ,
            'status' ,
        ];
        protected $casts = [
            'validity_period' => 'array',
            'specific_days_of_week' => 'array',
            'holidays_occasions' => 'array',
            'custom_blackout_dates' => 'array',
            'usage_limit_per_user' => 'array',
            'usage_limit_per_store' => 'array',
            'general_restrictions' => 'array',
        ];

    }





