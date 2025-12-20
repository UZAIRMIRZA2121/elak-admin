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
            'age_restriction' => 'array',
            'group_size_requirement' => 'array',
        ];

  

    /** 🔗 Relation: VoucherSetting → Item */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    /** 🔗 Relation: VoucherSetting → HolidayOccasions */
    public function holidays()
    {
        return HolidayOccasion::whereIn('id', $this->getArray($this->holidays_occasions))->get();
    }

    /** Accessor for full HolidayOccasion objects */
    public function getHolidayOccasionsAttribute()
    {
        return HolidayOccasion::whereIn('id', $this->getArray($this->holidays_occasions))->get();
    }

    /** 🔗 Relation: VoucherSetting → CustomBlackoutData */
    public function blackoutDates()
    {
        return CustomBlackoutData::whereIn('id', $this->getArray($this->custom_blackout_dates))->get();
    }

    /** Accessor for full CustomBlackoutData objects */
    public function getCustomBlackoutDatesAttribute()
    {
        return CustomBlackoutData::whereIn('id', $this->getArray($this->custom_blackout_dates))->get();
    }

    /** Helper function to safely decode array from JSON or array */
    private function getArray($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return [];
    }

}
