<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class GeneralRestriction extends Model
{
  use HasFactory;

  protected $fillable = ['name_ar', 'name_en'];

  public function voucherSettings()
  {
    return $this->belongsToMany(
      VoucherSetting::class,
      'voucher_setting_general_restriction'
    );
  }
}
