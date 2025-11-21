<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsagePeriod extends Model
{
    use HasFactory;

    protected $table = 'usage_periods';

    protected $fillable = ['name_ar', 'name_en'];
}
