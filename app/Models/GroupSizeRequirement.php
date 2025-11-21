<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSizeRequirement extends Model
{
    use HasFactory;

    protected $table = 'group_size_requirements';

    protected $fillable = ['name_ar', 'name_en'];
}
