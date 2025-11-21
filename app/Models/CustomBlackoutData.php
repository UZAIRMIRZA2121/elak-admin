<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomBlackoutData extends Model
{
    use HasFactory;

    protected $table = 'custom_blackout_data';

    protected $fillable = ['date', 'description'];
}
