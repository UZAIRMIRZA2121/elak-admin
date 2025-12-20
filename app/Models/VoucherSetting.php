<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherSetting extends Model
{
    use HasFactory;

    protected $table = "voucher_settings";

    protected $fillable = [
        'item_id',
        'validity_period',
        'specific_days_of_week',
        'holidays_occasions',
        'custom_blackout_dates',
        'age_restriction',
        'group_size_requirement',
        'usage_limit_per_user',
        'usage_limit_per_store',
        'offer_validity_after_purchase',
        'general_restrictions',
        'status',
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
        return HolidayOccasion::whereIn('id', $this->holidays_occasions ?? [])->get();
    }

    /** Optional: accessor to always return structured holidays */
    /** Accessor for full HolidayOccasion objects */
    public function getHolidayOccasionsAttribute()
    {
        // $this->holidays_occasions is casted to array
        $ids = is_array($this->holidays_occasions) ? $this->holidays_occasions : json_decode($this->holidays_occasions, true) ?? [];

        return HolidayOccasion::whereIn('id', $ids)->get();
    }
    /** 🔗 Relation: VoucherSetting → CustomBlackoutData */


    /** Accessor for full CustomBlackoutData objects */
    public function getCustomBlackoutDatesAttribute()
    {
        $value = $this->attributes['custom_blackout_dates'] ?? null;

        $ids = [];

        if (is_array($value)) {
            $ids = $value;
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);

            // If still a string, decode again
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }

            if (is_array($decoded)) {
                $ids = $decoded;
            }
        }

        // Now $ids is guaranteed to be an array
        return CustomBlackoutData::whereIn('id', $ids)->get();
    }


}
