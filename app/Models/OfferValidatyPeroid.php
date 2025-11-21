<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferValidatyPeroid extends Model
{
    use HasFactory;

    protected $table = 'offer_validaty_peroids';

    protected $fillable = ['name_ar', 'name_en'];
}
