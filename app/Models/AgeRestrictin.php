<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeRestrictin extends Model
{
    use HasFactory;

    protected $table = 'age_restrictins';

    protected $fillable = ['name_ar', 'name_en'];
}




